<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Models\Incapacidad;
use App\Models\Contrato;
use App\Models\Usuarios;
use App\Models\Area;
use App\Models\Hojasvida;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

/**
 * @OA\Tag(
 *     name="Incapacidades Jefe",
 *     description="Gestión de solicitudes de incapacidades por parte del jefe de personal. Este módulo permite al jefe de personal gestionar las solicitudes de incapacidades de los empleados de su área, incluyendo aprobar, rechazar y consultar solicitudes."
 * )
 */

class IncapacidadesJefeController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/solicitudes-incapacidades-jefe",
     *     summary="Obtener todas las solicitudes de incapacidades del área del jefe",
     *     tags={"Incapacidades Jefe"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Lista de solicitudes obtenida correctamente",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(
     *                 @OA\Property(property="idIncapacidad", type="integer", example=1),
     *                 @OA\Property(property="archivo", type="string", example="incapacidad_2024_001.pdf"),
     *                 @OA\Property(property="fechaInicio", type="string", format="date", example="2024-01-15"),
     *                 @OA\Property(property="fechaFinal", type="string", format="date", example="2024-01-20"),
     *                 @OA\Property(property="contratoId", type="integer", example=123),
     *                 @OA\Property(property="estado", type="string", example="pendiente", enum={"pendiente", "aprobado", "rechazado"}),
     *                 @OA\Property(
     *                     property="empleado",
     *                     type="object",
     *                     @OA\Property(property="numDocumento", type="string", example="12345678"),
     *                     @OA\Property(property="nombre", type="string", example="María José"),
     *                     @OA\Property(property="apellido", type="string", example="Rodríguez López")
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
    public function obtenerSolicitudesIncapacidades(Request $request): JsonResponse
    {
        try {
            // Obtener el usuario autenticado (jefe de personal)
            $usuario = Auth::user();
            if (!$usuario) {
                return response()->json(['error' => 'Usuario no autenticado'], 401);
            }

            // Buscar el área donde el usuario es jefe
            $area = Area::where('idJefe', $usuario->id)->first();
            if (!$area) {
                return response()->json(['error' => 'No se encontró área asignada'], 404);
            }

            // Obtener todas las incapacidades de empleados del área
            $solicitudes = DB::table('incapacidad as i')
                ->join('contrato as c', 'i.contratoId', '=', 'c.idContrato')
                ->join('hojasvida as h', 'c.hojaDeVida', '=', 'h.idHojaDeVida')
                ->join('usuarios as u', 'h.usuarioNumDocumento', '=', 'u.numDocumento')
                ->where('c.area', $area->idArea)
                ->where('c.cargoArea', 1) // Solo empleados, no jefes
                ->select([
                    'i.idIncapacidad',
                    'i.archivo',
                    'i.fechaInicio',
                    'i.fechaFinal',
                    'i.contratoId',
                    'i.estado',
                    'u.numDocumento',
                    DB::raw("CONCAT(u.primerNombre, ' ', COALESCE(u.segundoNombre, '')) as nombre"),
                    DB::raw("CONCAT(u.primerApellido, ' ', COALESCE(u.segundoApellido, '')) as apellido")
                ])
                ->orderBy('i.fechaInicio', 'desc')
                ->get();

            // Transformar los datos para el frontend
            $solicitudesFormateadas = $solicitudes->map(function ($solicitud) {
                $estadoTexto = 'pendiente';
                if ($solicitud->estado == 1) $estadoTexto = 'aprobado';
                if ($solicitud->estado == 2) $estadoTexto = 'rechazado';
                return [
                    'idIncapacidad' => $solicitud->idIncapacidad,
                    'archivo' => $solicitud->archivo,
                    'fechaInicio' => $solicitud->fechaInicio,
                    'fechaFinal' => $solicitud->fechaFinal,
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
                'error' => 'Error al obtener las solicitudes de incapacidades',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/solicitudes-incapacidades-jefe/{id}",
     *     summary="Obtener una solicitud específica de incapacidad",
     *     tags={"Incapacidades Jefe"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID de la solicitud de incapacidad",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Solicitud obtenida correctamente",
     *         @OA\JsonContent(
     *             @OA\Property(property="idIncapacidad", type="integer", example=1),
     *             @OA\Property(property="archivo", type="string", example="incapacidad_2024_001.pdf"),
     *             @OA\Property(property="fechaInicio", type="string", format="date", example="2024-01-15"),
     *             @OA\Property(property="fechaFinal", type="string", format="date", example="2024-01-20"),
     *             @OA\Property(property="contratoId", type="integer", example=123),
     *             @OA\Property(property="estado", type="string", example="pendiente", enum={"pendiente", "aprobado", "rechazado"}),
     *             @OA\Property(
     *                 property="empleado",
     *                 type="object",
     *                 @OA\Property(property="numDocumento", type="string", example="12345678"),
     *                 @OA\Property(property="nombre", type="string", example="María José"),
     *                 @OA\Property(property="apellido", type="string", example="Rodríguez López")
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
            $solicitud = DB::table('incapacidad as i')
                ->join('contrato as c', 'i.contratoId', '=', 'c.idContrato')
                ->join('hojasvida as h', 'c.hojaDeVida', '=', 'h.idHojaDeVida')
                ->join('usuarios as u', 'h.usuarioNumDocumento', '=', 'u.numDocumento')
                ->where('i.idIncapacidad', $id)
                ->select([
                    'i.idIncapacidad',
                    'i.archivo',
                    'i.fechaInicio',
                    'i.fechaFinal',
                    'i.contratoId',
                    'i.estado',
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
                'idIncapacidad' => $solicitud->idIncapacidad,
                'archivo' => $solicitud->archivo,
                'fechaInicio' => $solicitud->fechaInicio,
                'fechaFinal' => $solicitud->fechaFinal,
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
     *     path="/api/solicitudes-incapacidades-jefe/{id}/aprobar",
     *     summary="Aprobar una solicitud de incapacidad",
     *     tags={"Incapacidades Jefe"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID de la solicitud de incapacidad",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\RequestBody(
     *         required=false,
     *         @OA\JsonContent(
     *             @OA\Property(property="comentario", type="string", example="Incapacidad aprobada con documentación completa", maxLength=500)
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

            $solicitud = Incapacidad::find($id);
            if (!$solicitud) {
                return response()->json(['error' => 'Solicitud no encontrada'], 404);
            }

            // Verificar que el jefe tenga permisos sobre esta solicitud
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
     *     path="/api/solicitudes-incapacidades-jefe/{id}/rechazar",
     *     summary="Rechazar una solicitud de incapacidad",
     *     tags={"Incapacidades Jefe"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID de la solicitud de incapacidad",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\RequestBody(
     *         required=false,
     *         @OA\JsonContent(
     *             @OA\Property(property="comentario", type="string", example="Incapacidad rechazada por documentación incompleta", maxLength=500)
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

            $solicitud = Incapacidad::find($id);
            if (!$solicitud) {
                return response()->json(['error' => 'Solicitud no encontrada'], 404);
            }

            // Verificar que el jefe tenga permisos sobre esta solicitud
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
     *     path="/api/solicitudes-incapacidades-jefe/estadisticas",
     *     summary="Obtener estadísticas de solicitudes de incapacidades",
     *     tags={"Incapacidades Jefe"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Estadísticas obtenidas correctamente",
     *         @OA\JsonContent(
     *             @OA\Property(property="total", type="integer", example=15),
     *             @OA\Property(property="pendientes", type="integer", example=8),
     *             @OA\Property(property="aprobadas", type="integer", example=6),
     *             @OA\Property(property="rechazadas", type="integer", example=1)
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

            $total = DB::table('incapacidad as i')
                ->join('contrato as c', 'i.contratoId', '=', 'c.idContrato')
                ->where('c.area', $area->idArea)
                ->where('c.cargoArea', 1)
                ->count();

            return response()->json([
                'total' => $total,
                'pendientes' => $total, // Por ahora todas están pendientes
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
     * Verificar que el jefe tenga permisos sobre un contrato
     */
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

    /**
     * @OA\Put(
     *     path="/api/solicitudes-incapacidades-jefe/{id}/estado",
     *     summary="Actualizar el estado de una solicitud de incapacidad",
     *     tags={"Incapacidades Jefe"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID de la solicitud de incapacidad",
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

            $solicitud = Incapacidad::find($id);
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
} 