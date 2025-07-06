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

class IncapacidadesJefeController extends Controller
{
    /**
     * Obtener todas las solicitudes de incapacidades de empleados del área del jefe
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
                    'u.numDocumento',
                    DB::raw("CONCAT(u.primerNombre, ' ', COALESCE(u.segundoNombre, '')) as nombre"),
                    DB::raw("CONCAT(u.primerApellido, ' ', COALESCE(u.segundoApellido, '')) as apellido")
                ])
                ->orderBy('i.fechaInicio', 'desc')
                ->get();

            // Transformar los datos para el frontend
            $solicitudesFormateadas = $solicitudes->map(function ($solicitud) {
                return [
                    'idIncapacidad' => $solicitud->idIncapacidad,
                    'archivo' => $solicitud->archivo,
                    'fechaInicio' => $solicitud->fechaInicio,
                    'fechaFinal' => $solicitud->fechaFinal,
                    'contratoId' => $solicitud->contratoId,
                    'estado' => 'pendiente', // Por defecto pendiente ya que la tabla incapacidad no tiene estado
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
     * Obtener una solicitud específica
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
                    'u.numDocumento',
                    DB::raw("CONCAT(u.primerNombre, ' ', COALESCE(u.segundoNombre, '')) as nombre"),
                    DB::raw("CONCAT(u.primerApellido, ' ', COALESCE(u.segundoApellido, '')) as apellido")
                ])
                ->first();

            if (!$solicitud) {
                return response()->json(['error' => 'Solicitud no encontrada'], 404);
            }

            $solicitudFormateada = [
                'idIncapacidad' => $solicitud->idIncapacidad,
                'archivo' => $solicitud->archivo,
                'fechaInicio' => $solicitud->fechaInicio,
                'fechaFinal' => $solicitud->fechaFinal,
                'contratoId' => $solicitud->contratoId,
                'estado' => 'pendiente',
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
     * Aprobar una solicitud de incapacidad
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

            // Aquí podrías agregar lógica para actualizar el estado
            // Por ahora solo retornamos éxito ya que la tabla incapacidad no tiene campo estado
            // $solicitud->estado = 'Aprobado';
            // $solicitud->save();

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
     * Rechazar una solicitud de incapacidad
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

            // Aquí podrías agregar lógica para actualizar el estado
            // Por ahora solo retornamos éxito ya que la tabla incapacidad no tiene campo estado
            // $solicitud->estado = 'rechazado';
            // $solicitud->save();

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
     * Obtener estadísticas de solicitudes
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
} 