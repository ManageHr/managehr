<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Postulaciones;
use App\Models\Usuarios;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

/**
 * @OA\Tag(
 *     name="Postulaciones",
 *     description="Operaciones relacionadas con postulaciones de usuarios a vacantes"
 * )
 */
 

class PostulacionesController extends Controller
{

     /**
     * @OA\Get(
     *     path="/api/postulaciones",
     *     summary="Listar todas las postulaciones",
     *     tags={"Postulaciones"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="vacantesId",
     *         in="query",
     *         required=false,
     *         description="Filtrar por ID de vacante",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Lista de postulaciones"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Error interno"
     *     )
     * )
     */


    public function index(Request $request)
    {
        try {
            $query = Postulaciones::with(['usuario']); // <-- Aquí incluimos la relación

            if ($request->has('vacantesId')) {
                $query->where('vacantesId', $request->input('vacantesId'));
            }

            $postulaciones = $query->get();

            return response()->json([
                'data' => $postulaciones
            ], 200);
        } catch (\Exception $e) {
            Log::error('Error al obtener lista de postulaciones (PostulacionesController::index): ' . $e->getMessage());
            return response()->json([
                'message' => 'Ocurrió un error al obtener las postulaciones.',
                'error' => $e->getMessage()
            ], 500);
        }
    }


     /**
     * @OA\Get(
     *     path="/api/postulaciones/{id}",
     *     summary="Obtener una postulación por ID",
     *     tags={"Postulaciones"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID de la postulación",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(response=200, description="Postulación encontrada"),
     *     @OA\Response(response=404, description="Postulación no encontrada")
     * )
     */

    public function show($idPostulaciones)
    {
        try {
            $postulacion = Postulaciones::find($idPostulaciones);

            if (!$postulacion) {
                return response()->json(['message' => 'Postulación no encontrada'], 404);
            }

            return response()->json([
                'data' => $postulacion
            ], 200);
        } catch (\Exception $e) {
            Log::error('Error al obtener postulación con ID ' . $idPostulaciones . ' (PostulacionesController::show): ' . $e->getMessage());
            return response()->json([
                'message' => 'Ocurrió un error al obtener la postulación.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/postulaciones/buscar/vacante/{vacantesId}",
     *     summary="Buscar postulaciones por vacante",
     *     tags={"Postulaciones"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="vacantesId",
     *         in="path",
     *         required=true,
     *         description="ID de la vacante",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(response=200, description="Resultados encontrados"),
     *     @OA\Response(response=400, description="ID inválido"),
     *     @OA\Response(response=500, description="Error al buscar")
     * )
     */

    public function searchByVacantesId($vacantesId)
    {
        try {
            if (empty($vacantesId) || !is_numeric($vacantesId)) {
                return response()->json([
                    'message' => 'Parámetro vacantesId inválido o faltante.'
                ], 400);
            }

            $vacantesId = (int) $vacantesId;

            $postulaciones = Postulaciones::where('vacantesId', $vacantesId)->get();

            return response()->json([
                'results' => $postulaciones
            ], 200);
        } catch (\Exception $e) {
            Log::error('Error al buscar postulaciones por Vacante ID (PostulacionesController::searchByVacantesId): ' . $e->getMessage());
            return response()->json([
                'message' => 'Ocurrió un error al realizar la búsqueda.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * @OA\Put(
     *     path="/api/postulaciones/{id}/estado",
     *     summary="Actualizar el estado de una postulación",
     *     tags={"Postulaciones"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID de la postulación",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         @OA\JsonContent(
     *             required={"estado"},
     *             @OA\Property(property="estado", type="integer", example=2)
     *         )
     *     ),
     *     @OA\Response(response=200, description="Estado actualizado"),
     *     @OA\Response(response=404, description="Postulación no encontrada"),
     *     @OA\Response(response=500, description="Error al actualizar")
     * )
     */

    public function updateStatus(Request $request, $idPostulaciones)
    {
        try {
            $postulacion = Postulaciones::find($idPostulaciones);

            if (!$postulacion) {
                return response()->json(['message' => 'Postulación no encontrada'], 404);
            }

            $postulacion->estado = $request->input('estado');
            $postulacion->save();

            return response()->json([
                'message' => 'Estado actualizado correctamente.',
                'data' => $postulacion
            ], 200);
        } catch (\Exception $e) {
            Log::error('Error al actualizar estado de postulación (PostulacionesController::updateStatus): ' . $e->getMessage());
            return response()->json([
                'message' => 'Ocurrió un error al actualizar el estado.',
                'error' => $e->getMessage()
            ], 500);
        }
    }


    /**
     * @OA\Post(
     *     path="/api/postulaciones",
     *     summary="Registrar nueva postulación",
     *     tags={"Postulaciones"},
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         @OA\JsonContent(
     *             required={"vacantesId"},
     *             @OA\Property(property="vacantesId", type="integer", example=5)
     *         )
     *     ),
     *     @OA\Response(response=201, description="Postulación registrada"),
     *     @OA\Response(response=401, description="Usuario no válido"),
     *     @OA\Response(response=409, description="Postulación duplicada"),
     *     @OA\Response(response=500, description="Error al registrar")
     * )
     */


    public function store(Request $request)
    {
        try {
            $user = Auth::user();

            // Buscar el perfil del usuario autenticado usando su correo
            $usuario = Usuarios::where('email', $user->email)->first();

            if (!$usuario || !$usuario->numDocumento) {
                return response()->json([
                    'message' => 'No se encontró un perfil válido para este usuario.'
                ], 401);
            }

            $vacanteId = $request->input('vacantesId');

            // Validar si ya se postuló a esta vacante
            $yaPostulado = Postulaciones::where('numdocumento', $usuario->numDocumento)
                ->where('vacantesId', $vacanteId)
                ->exists();

            if ($yaPostulado) {
                return response()->json([
                    'message' => 'No puedes postularte nuevamente a esta vacante.'
                ], 409); // 409 Conflict
            }

            // Crear nueva postulación
            $postulacion = Postulaciones::create([
                'fechaPostulacion' => Carbon::now(),
                'estado' => 1,
                'vacantesId' => $vacanteId,
                'numdocumento' => $usuario->numDocumento,
            ]);

            return response()->json([
                'message' => 'Postulación registrada exitosamente.',
                'data' => $postulacion
            ], 201);
        } catch (\Exception $e) {
            Log::error("Error al registrar postulación: " . $e->getMessage());
            return response()->json([
                'message' => 'Error al registrar la postulación',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/postulaciones/reporte/vacantes",
     *     summary="Reporte por vacante",
     *     tags={"Postulaciones"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(response=200, description="Reporte generado"),
     *     @OA\Response(response=500, description="Error en reporte")
     * )
     */


    public function porVacante()
    {
        try {
            $reporte = Postulaciones::with('vacante')
                ->get()
                ->groupBy('vacantesId')
                ->map(function ($grupo) {
                    $vacante = $grupo->first()->vacante;
                    return [
                        'vacantesId' => $grupo->first()->vacantesId,
                        'nombreVacante' => $vacante ? $vacante->nomVacante : 'Sin nombre',
                        'totalPostulantes' => $grupo->count(),
                        'postulantes' => $grupo->map(function ($p) {
                            return [
                                'nombre' => $p->usuario->primerNombre . ' ' . $p->usuario->primerApellido,
                                'documento' => $p->usuario->numDocumento,
                                'correo' => $p->usuario->correo
                            ];
                        })->values()
                    ];
                })->values();

            return response()->json(['data' => $reporte]);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error en el reporte', 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/postulaciones/reporte/estado",
     *     summary="Reporte por estado",
     *     tags={"Postulaciones"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(response=200, description="Reporte generado"),
     *     @OA\Response(response=500, description="Error en reporte")
     * )
     */

    // 2. Por estado (aceptado, rechazado, pendiente)
    public function porEstado()
    {
        try {
            $postulaciones = Postulaciones::with('usuario')
                ->get()
                ->groupBy('estado')
                ->map(function ($items, $estado) {
                    return [
                        'estado' => $estado,
                        'total' => $items->count(),
                        'postulantes' => $items->map(function ($item) {
                            return [
                                'nombre' => $item->usuario->primerNombre . ' ' . $item->usuario->primerApellido,
                                'documento' => $item->usuario->numDocumento,
                                'correo' => $item->usuario->email,
                            ];
                        }),
                    ];
                })
                ->values();

            return response()->json(['data' => $postulaciones], 200);
        } catch (\Exception $e) {
            Log::error("Error en reportePorEstado: " . $e->getMessage());
            return response()->json(['message' => 'Error al generar el reporte por estado.', 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/postulaciones/reporte/empleado",
     *     summary="Reporte de empleados internos",
     *     tags={"Postulaciones"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(response=200, description="Reporte generado"),
     *     @OA\Response(response=500, description="Error en reporte")
     * )
     */

    // 3. Por empleados internos (usuarios con rol ≠ 5)
    public function porEmpleado()
    {
        try {
            $postulaciones = Postulaciones::with(['usuario.user', 'vacante']) // 👈 AÑADIDO
                ->get()
                ->filter(function ($item) {
                    return $item->usuario->user->rol != 5;
                })
                ->groupBy(function ($item) {
                    return $item->usuario->user->rol ?? 'Sin Rol';
                })
                ->map(function ($items, $rolId) {
                    return [
                        'rolId' => $rolId,
                        'total' => $items->count(),
                        'postulantes' => $items->map(function ($item) {
                            return [
                                'nombre' => $item->usuario->primerNombre . ' ' . $item->usuario->primerApellido,
                                'documento' => $item->usuario->numDocumento,
                                'correo' => $item->usuario->email,
                                'vacante' => $item->vacante->nomVacante ?? 'No especificada' // 👈 Añadido
                            ];
                        }),
                    ];
                })
                ->values();

            return response()->json(['data' => $postulaciones], 200);
        } catch (\Exception $e) {
            Log::error("Error en reporteEmpleadosInternos: " . $e->getMessage());
            return response()->json(['message' => 'Error al generar el reporte de empleados internos.', 'error' => $e->getMessage()], 500);
        }
    }
}
