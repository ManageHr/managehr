<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\Area;
use App\Models\Contrato;
use App\Models\Horasextra;

/**
 * @OA\Tag(
 *     name="Horas Extra Jefe",
 *     description="Gestión y aprobación de solicitudes de horas extra por parte del jefe de personal. Permite consultar, aprobar o rechazar solicitudes de su área."
 * )
 */

class HorasExtraJefeController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/solicitudes-horasextra-jefe/solicitudes",
     *     summary="Obtener todas las solicitudes de horas extra del área del jefe",
     *     tags={"Horas Extra Jefe"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Lista de solicitudes obtenida correctamente",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(
     *                 @OA\Property(property="idHorasExtra", type="integer", example=1),
     *                 @OA\Property(property="descripcion", type="string", example="Trabajo adicional en proyecto urgente"),
     *                 @OA\Property(property="fecha", type="string", format="date", example="2024-01-15"),
     *                 @OA\Property(property="tipoHorasId", type="integer", example=1),
     *                 @OA\Property(property="nHorasExtra", type="integer", example=4),
     *                 @OA\Property(property="contratoId", type="integer", example=123),
     *                 @OA\Property(property="estado", type="string", example="pendiente", enum={"pendiente", "aprobado", "rechazado"}),
     *                 @OA\Property(
     *                     property="empleado",
     *                     type="object",
     *                     @OA\Property(property="numDocumento", type="string", example="12345678"),
     *                     @OA\Property(property="nombre", type="string", example="Juan Carlos"),
     *                     @OA\Property(property="apellido", type="string", example="Pérez García")
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Usuario no autenticado"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="No se encontró área asignada"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Error interno del servidor"
     *     )
     * )
     */
    public function obtenerSolicitudesHorasExtra(Request $request): JsonResponse
    {
        try {
            $usuario = Auth::user();
            if (!$usuario) {
                return response()->json(['error' => 'Usuario no autenticado'], 401);
            }
            $area = Area::where('idJefe', $usuario->id)->first();
            if (!$area) {
                return response()->json(['error' => 'No se encontró área asignada'], 404);
            }
            $solicitudes = DB::table('horasextra as h')
                ->join('contrato as c', 'h.contratoId', '=', 'c.idContrato')
                ->join('hojasvida as hv', 'c.hojaDeVida', '=', 'hv.idHojaDeVida')
                ->join('usuarios as u', 'hv.usuarioNumDocumento', '=', 'u.numDocumento')
                ->where('c.area', $area->idArea)
                ->where('c.cargoArea', 1)
                ->select([
                    'h.idHorasExtra',
                    'h.descripcion',
                    'h.fecha',
                    'h.tipoHorasId',
                    'h.nHorasExtra',
                    'h.contratoId',
                    'h.estado',
                    'u.numDocumento',
                    DB::raw("CONCAT(u.primerNombre, ' ', COALESCE(u.segundoNombre, '')) as nombre"),
                    DB::raw("CONCAT(u.primerApellido, ' ', COALESCE(u.segundoApellido, '')) as apellido")
                ])
                ->orderBy('h.fecha', 'desc')
                ->get();
            $solicitudesFormateadas = $solicitudes->map(function ($solicitud) {
                $estadoTexto = 'pendiente';
                if ($solicitud->estado == 1) $estadoTexto = 'aprobado';
                if ($solicitud->estado == 2) $estadoTexto = 'rechazado';
                return [
                    'idHorasExtra' => $solicitud->idHorasExtra,
                    'descripcion' => $solicitud->descripcion,
                    'fecha' => $solicitud->fecha,
                    'tipoHorasId' => $solicitud->tipoHorasId,
                    'nHorasExtra' => $solicitud->nHorasExtra,
                    'contratoId' => $solicitud->contratoId,
                    'estado' => $estadoTexto,
                    'empleado' => [
                        'numDocumento' => $solicitud->numDocumento,
                        'nombre' => trim($solicitud->nombre),
                        'apellido' => trim($solicitud->apellido)
                    ]
                ];
            });
            return response()->json($solicitudesFormateadas);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Error al obtener las solicitudes de horas extra',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/solicitudes-horasextra-jefe/{id}",
     *     summary="Obtener una solicitud específica de horas extra",
     *     tags={"Horas Extra Jefe"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID de la solicitud de horas extra",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Solicitud obtenida correctamente",
     *         @OA\JsonContent(
     *             @OA\Property(property="idHorasExtra", type="integer", example=1),
     *             @OA\Property(property="descripcion", type="string", example="Trabajo adicional en proyecto urgente"),
     *             @OA\Property(property="fecha", type="string", format="date", example="2024-01-15"),
     *             @OA\Property(property="tipoHorasId", type="integer", example=1),
     *             @OA\Property(property="nHorasExtra", type="integer", example=4),
     *             @OA\Property(property="contratoId", type="integer", example=123),
     *             @OA\Property(property="estado", type="string", example="pendiente", enum={"pendiente", "aprobado", "rechazado"}),
     *             @OA\Property(
     *                 property="empleado",
     *                 type="object",
     *                 @OA\Property(property="numDocumento", type="string", example="12345678"),
     *                 @OA\Property(property="nombre", type="string", example="Juan Carlos"),
     *                 @OA\Property(property="apellido", type="string", example="Pérez García")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Solicitud no encontrada"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Error interno del servidor"
     *     )
     * )
     */
    public function obtenerSolicitud($id): JsonResponse
    {
        try {
            $solicitud = DB::table('horasextra as h')
                ->join('contrato as c', 'h.contratoId', '=', 'c.idContrato')
                ->join('hojasvida as hv', 'c.hojaDeVida', '=', 'hv.idHojaDeVida')
                ->join('usuarios as u', 'hv.usuarioNumDocumento', '=', 'u.numDocumento')
                ->where('h.idHorasExtra', $id)
                ->select([
                    'h.idHorasExtra',
                    'h.descripcion',
                    'h.fecha',
                    'h.tipoHorasId',
                    'h.nHorasExtra',
                    'h.contratoId',
                    'h.estado',
                    'u.numDocumento',
                    DB::raw("CONCAT(u.primerNombre, ' ', COALESCE(u.segundoNombre, '')) as nombre"),
                    DB::raw("CONCAT(u.primerApellido, ' ', COALESCE(u.segundoApellido, '')) as apellido")
                ])
                ->first();
            if (!$solicitud) {
                return response()->json(['error' => 'Solicitud no encontrada'], 404);
            }
            $estadoTexto = 'pendiente';
            if ($solicitud->estado == 1) $estadoTexto = 'aprobado';
            if ($solicitud->estado == 2) $estadoTexto = 'rechazado';
            $solicitudFormateada = [
                'idHorasExtra' => $solicitud->idHorasExtra,
                'descripcion' => $solicitud->descripcion,
                'fecha' => $solicitud->fecha,
                'tipoHorasId' => $solicitud->tipoHorasId,
                'nHorasExtra' => $solicitud->nHorasExtra,
                'contratoId' => $solicitud->contratoId,
                'estado' => $estadoTexto,
                'empleado' => [
                    'numDocumento' => $solicitud->numDocumento,
                    'nombre' => trim($solicitud->nombre),
                    'apellido' => trim($solicitud->apellido)
                ]
            ];
            return response()->json($solicitudFormateada);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Error al obtener la solicitud',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * @OA\Post(
     *     path="/api/solicitudes-horasextra-jefe/{id}/aprobar",
     *     summary="Aprobar una solicitud de horas extra",
     *     tags={"Horas Extra Jefe"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID de la solicitud de horas extra",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\RequestBody(
     *         required=false,
     *         @OA\JsonContent(
     *             @OA\Property(property="comentario", type="string", example="Solicitud aprobada por cumplir con los requisitos", maxLength=500)
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Solicitud aprobada exitosamente",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Solicitud aprobada exitosamente"),
     *             @OA\Property(property="solicitud", type="object")
     *         )
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="No tiene permisos para gestionar esta solicitud"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Solicitud no encontrada"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Error interno del servidor"
     *     )
     * )
     */
    public function aprobarSolicitud(Request $request, $id): JsonResponse
    {
        try {
            $request->validate([
                'comentario' => 'nullable|string|max:500'
            ]);
            $solicitud = Horasextra::find($id);
            if (!$solicitud) {
                return response()->json(['error' => 'Solicitud no encontrada'], 404);
            }
            $tienePermisos = $this->verificarPermisosJefe($solicitud->contratoId);
            if (!$tienePermisos) {
                return response()->json(['error' => 'No tiene permisos para gestionar esta solicitud'], 403);
            }
            $solicitud->estado = 1; // Aprobado
            $solicitud->save();
            return response()->json([
                'message' => 'Solicitud aprobada exitosamente',
                'solicitud' => $solicitud
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Error al aprobar la solicitud',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * @OA\Post(
     *     path="/api/solicitudes-horasextra-jefe/{id}/rechazar",
     *     summary="Rechazar una solicitud de horas extra",
     *     tags={"Horas Extra Jefe"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID de la solicitud de horas extra",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\RequestBody(
     *         required=false,
     *         @OA\JsonContent(
     *             @OA\Property(property="comentario", type="string", example="Solicitud rechazada por no cumplir con los requisitos", maxLength=500)
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Solicitud rechazada exitosamente",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Solicitud rechazada exitosamente"),
     *             @OA\Property(property="solicitud", type="object")
     *         )
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="No tiene permisos para gestionar esta solicitud"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Solicitud no encontrada"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Error interno del servidor"
     *     )
     * )
     */
    public function rechazarSolicitud(Request $request, $id): JsonResponse
    {
        try {
            $request->validate([
                'comentario' => 'nullable|string|max:500'
            ]);
            $solicitud = Horasextra::find($id);
            if (!$solicitud) {
                return response()->json(['error' => 'Solicitud no encontrada'], 404);
            }
            $tienePermisos = $this->verificarPermisosJefe($solicitud->contratoId);
            if (!$tienePermisos) {
                return response()->json(['error' => 'No tiene permisos para gestionar esta solicitud'], 403);
            }
            $solicitud->estado = 2; // Rechazado
            $solicitud->save();
            return response()->json([
                'message' => 'Solicitud rechazada exitosamente',
                'solicitud' => $solicitud
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Error al rechazar la solicitud',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/solicitudes-horasextra-jefe/estadisticas",
     *     summary="Obtener estadísticas de solicitudes de horas extra",
     *     tags={"Horas Extra Jefe"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Estadísticas obtenidas correctamente",
     *         @OA\JsonContent(
     *             @OA\Property(property="total", type="integer", example=25),
     *             @OA\Property(property="pendientes", type="integer", example=10),
     *             @OA\Property(property="aprobadas", type="integer", example=12),
     *             @OA\Property(property="rechazadas", type="integer", example=3)
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Usuario no autenticado"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="No se encontró área asignada"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Error interno del servidor"
     *     )
     * )
     */
    public function obtenerEstadisticas(): JsonResponse
    {
        try {
            $usuario = Auth::user();
            if (!$usuario) {
                return response()->json(['error' => 'Usuario no autenticado'], 401);
            }
            $area = Area::where('idJefe', $usuario->id)->first();
            if (!$area) {
                return response()->json(['error' => 'No se encontró área asignada'], 404);
            }
            $total = DB::table('horasextra as h')
                ->join('contrato as c', 'h.contratoId', '=', 'c.idContrato')
                ->where('c.area', $area->idArea)
                ->where('c.cargoArea', 1)
                ->count();
            return response()->json([
                'total' => $total,
                'pendientes' => $total,
                'aprobadas' => 0,
                'rechazadas' => 0
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Error al obtener estadísticas',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * @OA\Put(
     *     path="/api/solicitudes-horasextra-jefe/{id}/estado",
     *     summary="Actualizar el estado de una solicitud de horas extra",
     *     tags={"Horas Extra Jefe"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID de la solicitud de horas extra",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="estado", type="string", example="Aprobado", enum={"Pendiente", "Aprobado", "Rechazado"})
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Estado actualizado correctamente",
     *         @OA\JsonContent(
     *             @OA\Property(property="mensaje", type="string", example="Estado actualizado correctamente"),
     *             @OA\Property(property="solicitud", type="object"),
     *             @OA\Property(property="status", type="integer", example=200)
     *         )
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="No tiene permisos para gestionar esta solicitud"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Solicitud no encontrada"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Error interno del servidor"
     *     )
     * )
     */
    public function actualizarEstado(Request $request, $id): JsonResponse
    {
        try {
            $estadoTexto = ucfirst(strtolower(trim($request->estado)));
            $estados = ['Pendiente' => 0, 'Aprobado' => 1, 'Rechazado' => 2];
            $estado = $estados[$estadoTexto] ?? 0;

            $solicitud = Horasextra::find($id);
            if (!$solicitud) {
                return response()->json(['error' => 'Solicitud no encontrada'], 404);
            }

            $tienePermisos = $this->verificarPermisosJefe($solicitud->contratoId);
            if (!$tienePermisos) {
                return response()->json(['error' => 'No tiene permisos para gestionar esta solicitud'], 403);
            }

            $solicitud->estado = $estado;
            $solicitud->save();

            return response()->json([
                'mensaje' => 'Estado actualizado correctamente',
                'solicitud' => $solicitud,
                'status' => 200
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Error al actualizar el estado',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    private function verificarPermisosJefe($contratoId): bool
    {
        try {
            $usuario = Auth::user();
            $area = Area::where('idJefe', $usuario->id)->first();
            if (!$area) {
                return false;
            }
            $contrato = Contrato::where('idContrato', $contratoId)
                ->where('area', $area->idArea)
                ->where('cargoArea', 1)
                ->first();
            return $contrato !== null;
        } catch (\Exception $e) {
            return false;
        }
    }
} 