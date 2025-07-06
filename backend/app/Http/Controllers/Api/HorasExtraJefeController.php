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

class HorasExtraJefeController extends Controller
{
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
                    'u.numDocumento',
                    DB::raw("CONCAT(u.primerNombre, ' ', COALESCE(u.segundoNombre, '')) as nombre"),
                    DB::raw("CONCAT(u.primerApellido, ' ', COALESCE(u.segundoApellido, '')) as apellido")
                ])
                ->orderBy('h.fecha', 'desc')
                ->get();
            $solicitudesFormateadas = $solicitudes->map(function ($solicitud) {
                return [
                    'idHorasExtra' => $solicitud->idHorasExtra,
                    'descripcion' => $solicitud->descripcion,
                    'fecha' => $solicitud->fecha,
                    'tipoHorasId' => $solicitud->tipoHorasId,
                    'nHorasExtra' => $solicitud->nHorasExtra,
                    'contratoId' => $solicitud->contratoId,
                    'estado' => 'pendiente',
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
                    'u.numDocumento',
                    DB::raw("CONCAT(u.primerNombre, ' ', COALESCE(u.segundoNombre, '')) as nombre"),
                    DB::raw("CONCAT(u.primerApellido, ' ', COALESCE(u.segundoApellido, '')) as apellido")
                ])
                ->first();
            if (!$solicitud) {
                return response()->json(['error' => 'Solicitud no encontrada'], 404);
            }
            $solicitudFormateada = [
                'idHorasExtra' => $solicitud->idHorasExtra,
                'descripcion' => $solicitud->descripcion,
                'fecha' => $solicitud->fecha,
                'tipoHorasId' => $solicitud->tipoHorasId,
                'nHorasExtra' => $solicitud->nHorasExtra,
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
            // Aquí podrías agregar lógica para actualizar el estado
            // $solicitud->estado = 'Aprobado';
            // $solicitud->save();
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
            // Aquí podrías agregar lógica para actualizar el estado
            // $solicitud->estado = 'rechazado';
            // $solicitud->save();
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