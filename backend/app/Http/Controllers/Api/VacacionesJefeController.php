<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Models\Vacaciones;
use App\Models\Contrato;
use App\Models\Usuarios;
use App\Models\Area;
use App\Models\Hojasvida;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

/**
 * @OA\Tag(
 *     name="Vacaciones Jefe",
 *     description="Gestión de solicitudes de vacaciones por parte del jefe de personal. Este módulo permite al jefe de personal gestionar las solicitudes de vacaciones de los empleados de su área, incluyendo aprobar, rechazar y consultar solicitudes."
 * )
 */

class VacacionesJefeController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/jefe/vacaciones/solicitudes",
     *     summary="Obtener todas las solicitudes de vacaciones del área del jefe",
     *     tags={"Vacaciones Jefe"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Lista de solicitudes obtenida correctamente",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(
     *                 @OA\Property(property="idVacaciones", type="integer", example=1),
     *                 @OA\Property(property="motivo", type="string", example="Vacaciones familiares"),
     *                 @OA\Property(property="fechaInicio", type="string", format="date", example="2024-07-15"),
     *                 @OA\Property(property="fechaFinal", type="string", format="date", example="2024-07-25"),
     *                 @OA\Property(property="dias", type="integer", example=10),
     *                 @OA\Property(property="contratoId", type="integer", example=123),
     *                 @OA\Property(property="estado", type="string", example="pendiente", enum={"pendiente", "aprobado", "rechazado"}),
     *                 @OA\Property(
     *                     property="empleado",
     *                     type="object",
     *                     @OA\Property(property="numDocumento", type="string", example="12345678"),
     *                     @OA\Property(property="nombre", type="string", example="Carlos Andrés"),
     *                     @OA\Property(property="apellido", type="string", example="González Silva")
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
    public function obtenerSolicitudesVacaciones(Request $request): JsonResponse
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

            // Obtener todas las vacaciones de empleados del área
            $solicitudes = DB::table('vacaciones as v')
                ->join('contrato as c', 'v.contratoId', '=', 'c.idContrato')
                ->join('hojasvida as h', 'c.hojaDeVida', '=', 'h.idHojaDeVida')
                ->join('usuarios as u', 'h.usuarioNumDocumento', '=', 'u.numDocumento')
                ->where('c.area', $area->idArea)
                ->where('c.cargoArea', 1) // Solo empleados, no jefes
                ->select([
                    'v.idVacaciones',
                    'v.motivo',
                    'v.fechaInicio',
                    'v.fechaFinal',
                    'v.dias',
                    'v.estado',
                    'v.contratoId',
                    'u.numDocumento',
                    DB::raw("CONCAT(u.primerNombre, ' ', COALESCE(u.segundoNombre, '')) as nombre"),
                    DB::raw("CONCAT(u.primerApellido, ' ', COALESCE(u.segundoApellido, '')) as apellido")
                ])
                ->orderBy('v.fechaInicio', 'desc')
                ->get();

            // Transformar los datos para el frontend
            $solicitudesFormateadas = $solicitudes->map(function ($solicitud) {
                return [
                    'idVacaciones' => $solicitud->idVacaciones,
                    'motivo' => $solicitud->motivo,
                    'fechaInicio' => $solicitud->fechaInicio,
                    'fechaFinal' => $solicitud->fechaFinal,
                    'dias' => $solicitud->dias,
                    'contratoId' => $solicitud->contratoId,
                    'estado' => strtolower($solicitud->estado),
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
                'error' => 'Error al obtener las solicitudes de vacaciones',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/jefe/vacaciones/solicitudes/{id}",
     *     summary="Obtener una solicitud específica de vacaciones",
     *     tags={"Vacaciones Jefe"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID de la solicitud de vacaciones",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Solicitud obtenida correctamente",
     *         @OA\JsonContent(
     *             @OA\Property(property="idVacaciones", type="integer", example=1),
     *             @OA\Property(property="motivo", type="string", example="Vacaciones familiares"),
     *             @OA\Property(property="fechaInicio", type="string", format="date", example="2024-07-15"),
     *             @OA\Property(property="fechaFinal", type="string", format="date", example="2024-07-25"),
     *             @OA\Property(property="dias", type="integer", example=10),
     *             @OA\Property(property="contratoId", type="integer", example=123),
     *             @OA\Property(property="estado", type="string", example="pendiente", enum={"pendiente", "aprobado", "rechazado"}),
     *             @OA\Property(
     *                 property="empleado",
     *                 type="object",
     *                 @OA\Property(property="numDocumento", type="string", example="12345678"),
     *                 @OA\Property(property="nombre", type="string", example="Carlos Andrés"),
     *                 @OA\Property(property="apellido", type="string", example="González Silva")
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
            $solicitud = DB::table('vacaciones as v')
                ->join('contrato as c', 'v.contratoId', '=', 'c.idContrato')
                ->join('hojasvida as h', 'c.hojaDeVida', '=', 'h.idHojaDeVida')
                ->join('usuarios as u', 'h.usuarioNumDocumento', '=', 'u.numDocumento')
                ->where('v.idVacaciones', $id)
                ->select([
                    'v.idVacaciones',
                    'v.motivo',
                    'v.fechaInicio',
                    'v.fechaFinal',
                    'v.dias',
                    'v.estado',
                    'v.contratoId',
                    'u.numDocumento',
                    DB::raw("CONCAT(u.primerNombre, ' ', COALESCE(u.segundoNombre, '')) as nombre"),
                    DB::raw("CONCAT(u.primerApellido, ' ', COALESCE(u.segundoApellido, '')) as apellido")
                ])
                ->first();

            if (!$solicitud) {
                return response()->json(['error' => 'Solicitud no encontrada'], 404);
            }

            $solicitudFormateada = [
                'idVacaciones' => $solicitud->idVacaciones,
                'motivo' => $solicitud->motivo,
                'fechaInicio' => $solicitud->fechaInicio,
                'fechaFinal' => $solicitud->fechaFinal,
                'dias' => $solicitud->dias,
                'contratoId' => $solicitud->contratoId,
                'estado' => strtolower($solicitud->estado),
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
     *     path="/api/jefe/vacaciones/solicitudes/{id}/aprobar",
     *     summary="Aprobar una solicitud de vacaciones",
     *     tags={"Vacaciones Jefe"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID de la solicitud de vacaciones",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\RequestBody(
     *         required=false,
     *         @OA\JsonContent(
     *             @OA\Property(property="comentario", type="string", example="Vacaciones aprobadas según calendario laboral", maxLength=500)
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

            $solicitud = Vacaciones::find($id);
            if (!$solicitud) {
                return response()->json(['error' => 'Solicitud no encontrada'], 404);
            }

            // Verificar que el jefe tenga permisos sobre esta solicitud
            $tienePermisos = $this->verificarPermisosJefe($solicitud->contratoId);
            if (!$tienePermisos) {
                return response()->json(['error' => 'No tiene permisos para gestionar esta solicitud'], 403);
            }

            $solicitud->estado = 'Aprobado';
            $solicitud->save();

            // Aquí podrías agregar lógica para enviar notificaciones
            // $this->enviarNotificacionAprobacion($solicitud);

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
     *     path="/api/jefe/vacaciones/solicitudes/{id}/rechazar",
     *     summary="Rechazar una solicitud de vacaciones",
     *     tags={"Vacaciones Jefe"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID de la solicitud de vacaciones",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\RequestBody(
     *         required=false,
     *         @OA\JsonContent(
     *             @OA\Property(property="comentario", type="string", example="Vacaciones rechazadas por alta carga laboral", maxLength=500)
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

            $solicitud = Vacaciones::find($id);
            if (!$solicitud) {
                return response()->json(['error' => 'Solicitud no encontrada'], 404);
            }

            // Verificar que el jefe tenga permisos sobre esta solicitud
            $tienePermisos = $this->verificarPermisosJefe($solicitud->contratoId);
            if (!$tienePermisos) {
                return response()->json(['error' => 'No tiene permisos para gestionar esta solicitud'], 403);
            }

            $solicitud->estado = 'rechazado';
            $solicitud->save();

            // Aquí podrías agregar lógica para enviar notificaciones
            // $this->enviarNotificacionRechazo($solicitud);

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
     *     path="/api/jefe/vacaciones/estadisticas",
     *     summary="Obtener estadísticas de solicitudes de vacaciones",
     *     tags={"Vacaciones Jefe"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Estadísticas obtenidas correctamente",
     *         @OA\JsonContent(
     *             @OA\Property(property="total", type="integer", example=30),
     *             @OA\Property(property="pendientes", type="integer", example=12),
     *             @OA\Property(property="aprobadas", type="integer", example=15),
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

            $estadisticas = DB::table('vacaciones as v')
                ->join('contrato as c', 'v.contratoId', '=', 'c.idContrato')
                ->where('c.area', $area->idArea)
                ->where('c.cargoArea', 1)
                ->selectRaw('
                    COUNT(*) as total,
                    SUM(CASE WHEN v.estado = "Pendiente" THEN 1 ELSE 0 END) as pendientes,
                    SUM(CASE WHEN v.estado = "Aprobado" THEN 1 ELSE 0 END) as aprobadas,
                    SUM(CASE WHEN v.estado = "rechazado" THEN 1 ELSE 0 END) as rechazadas
                ')
                ->first();

            return response()->json([
                'total' => $estadisticas->total ?? 0,
                'pendientes' => $estadisticas->pendientes ?? 0,
                'aprobadas' => $estadisticas->aprobadas ?? 0,
                'rechazadas' => $estadisticas->rechazadas ?? 0
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
}
