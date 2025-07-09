<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Models\User;
use App\Models\Area;
use App\Models\Contrato;
use App\Models\Usuarios;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

/**
 * @OA\Tag(
 *     name="Jefe Personal",
 *     description="Gestión general del jefe de personal. Este módulo permite al jefe de personal gestionar información general de su área, incluyendo la consulta de empleados bajo su supervisión."
 * )
 */

class jefePersonalController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/jefe-personal/empleados/{jefeId}",
     *     summary="Obtener empleados bajo la supervisión de un jefe de personal",
     *     tags={"Jefe Personal"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="jefeId",
     *         in="path",
     *         required=true,
     *         description="ID del jefe de personal",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Empleados obtenidos correctamente",
     *         @OA\JsonContent(
     *             @OA\Property(property="empleados", type="array",
     *                 @OA\Items(
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="name", type="string", example="juan.perez@empresa.com"),
     *                     @OA\Property(property="email", type="string", example="juan.perez@empresa.com"),
     *                     @OA\Property(property="email_verified_at", type="string", format="date-time", example="2024-01-15T10:30:00Z"),
     *                     @OA\Property(property="created_at", type="string", format="date-time", example="2024-01-15T10:30:00Z"),
     *                     @OA\Property(property="updated_at", type="string", format="date-time", example="2024-01-15T10:30:00Z"),
     *                     @OA\Property(
     *                         property="perfil",
     *                         type="object",
     *                         @OA\Property(property="numDocumento", type="string", example="12345678"),
     *                         @OA\Property(property="primerNombre", type="string", example="Juan Carlos"),
     *                         @OA\Property(property="segundoNombre", type="string", example="Andrés"),
     *                         @OA\Property(property="primerApellido", type="string", example="Pérez"),
     *                         @OA\Property(property="segundoApellido", type="string", example="García"),
     *                         @OA\Property(property="fechaNacimiento", type="string", format="date", example="1990-05-15"),
     *                         @OA\Property(property="genero", type="string", example="Masculino"),
     *                         @OA\Property(property="telefono", type="string", example="3001234567"),
     *                         @OA\Property(property="direccion", type="string", example="Calle 123 #45-67"),
     *                         @OA\Property(property="ciudad", type="string", example="Bogotá"),
     *                         @OA\Property(property="usersId", type="integer", example=1)
     *                     )
     *                 )
     *             ),
     *             @OA\Property(property="area", type="string", example="Recursos Humanos"),
     *             @OA\Property(property="message", type="string", example="Empleados obtenidos correctamente")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="No se encontró área asignada para este jefe de personal",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="No se encontró área asignada para este jefe de personal"),
     *             @OA\Property(property="empleados", type="array", @OA\Items())
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Error interno del servidor"
     *     )
     * )
     */
    public function empleadosPorJefe($jefeId): JsonResponse
    {
        try {
            $area = DB::table('area')->where('idJefe', $jefeId)->first();
            if (!$area) {
                return response()->json([
                    'message' => 'No se encontró área asignada para este jefe de personal', 
                    'empleados' => []
                ], 404);
            }

            $contratos = DB::table('contrato')
                ->where('area', $area->idArea)
                ->where('cargoArea', 1)
                ->where('estado', 1)
                ->get();

            $empleados = [];
            foreach ($contratos as $contrato) {
                $hoja = DB::table('hojasvida')->where('idHojaDeVida', $contrato->hojaDeVida)->first();
                if ($hoja) {
                    $usuario = DB::table('usuarios')->where('numDocumento', $hoja->usuarioNumDocumento)->first();
                    if ($usuario) {
                        $user = DB::table('users')->where('id', $usuario->usersId)->first();
                        if ($user) {
                            $user->perfil = $usuario;
                            $empleados[] = $user;
                        }
                    }
                }
            }

            return response()->json([
                'empleados' => $empleados,
                'area' => $area->nombreArea,
                'message' => 'Empleados obtenidos correctamente'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Error al obtener empleados',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/jefe-personal",
     *     summary="Listar jefes de personal",
     *     tags={"Jefe Personal"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Lista de jefes de personal obtenida correctamente",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(
     *                 @OA\Property(property="idArea", type="integer", example=1),
     *                 @OA\Property(property="nombreArea", type="string", example="Recursos Humanos"),
     *                 @OA\Property(property="idJefe", type="integer", example=1),
     *                 @OA\Property(property="created_at", type="string", format="date-time"),
     *                 @OA\Property(property="updated_at", type="string", format="date-time")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Usuario no autenticado"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Error interno del servidor"
     *     )
     * )
     */
    public function index(): JsonResponse
    {
        try {
            $jefes = DB::table('area')
                ->whereNotNull('idJefe')
                ->select(['idArea', 'nombreArea', 'idJefe'])
                ->get();

            return response()->json([
                'jefes' => $jefes,
                'message' => 'Jefes de personal obtenidos correctamente'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Error al obtener jefes de personal',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * @OA\Post(
     *     path="/api/jefe-personal",
     *     summary="Crear nuevo jefe de personal",
     *     tags={"Jefe Personal"},
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="areaId", type="integer", example=1, description="ID del área"),
     *             @OA\Property(property="jefeId", type="integer", example=1, description="ID del usuario que será jefe")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Jefe de personal creado correctamente",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Jefe de personal asignado correctamente"),
     *             @OA\Property(property="area", type="object")
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Error de validación"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Error interno del servidor"
     *     )
     * )
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'areaId' => 'required|integer|exists:area,idArea',
                'jefeId' => 'required|integer|exists:users,id'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'error' => 'Error de validación',
                    'errors' => $validator->errors()
                ], 400);
            }

            $area = Area::find($request->areaId);
            $area->idJefe = $request->jefeId;
            $area->save();

            return response()->json([
                'message' => 'Jefe de personal asignado correctamente',
                'area' => $area
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Error al crear jefe de personal',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/jefe-personal/{id}",
     *     summary="Obtener jefe de personal específico",
     *     tags={"Jefe Personal"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID del área del jefe de personal",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Jefe de personal obtenido correctamente",
     *         @OA\JsonContent(
     *             @OA\Property(property="idArea", type="integer", example=1),
     *             @OA\Property(property="nombreArea", type="string", example="Recursos Humanos"),
     *             @OA\Property(property="idJefe", type="integer", example=1),
     *             @OA\Property(property="created_at", type="string", format="date-time"),
     *             @OA\Property(property="updated_at", type="string", format="date-time")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Jefe de personal no encontrado"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Error interno del servidor"
     *     )
     * )
     */
    public function show(string $id): JsonResponse
    {
        try {
            $area = DB::table('area')
                ->where('idArea', $id)
                ->whereNotNull('idJefe')
                ->first();

            if (!$area) {
                return response()->json([
                    'error' => 'Jefe de personal no encontrado'
                ], 404);
            }

            return response()->json($area);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Error al obtener jefe de personal',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * @OA\Put(
     *     path="/api/jefe-personal/{id}",
     *     summary="Actualizar jefe de personal",
     *     tags={"Jefe Personal"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID del área del jefe de personal",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="jefeId", type="integer", example=1, description="ID del nuevo jefe")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Jefe de personal actualizado correctamente",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Jefe de personal actualizado correctamente"),
     *             @OA\Property(property="area", type="object")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Jefe de personal no encontrado"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Error interno del servidor"
     *     )
     * )
     */
    public function update(Request $request, string $id): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'jefeId' => 'required|integer|exists:users,id'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'error' => 'Error de validación',
                    'errors' => $validator->errors()
                ], 400);
            }

            $area = Area::find($id);
            if (!$area) {
                return response()->json([
                    'error' => 'Jefe de personal no encontrado'
                ], 404);
            }

            $area->idJefe = $request->jefeId;
            $area->save();

            return response()->json([
                'message' => 'Jefe de personal actualizado correctamente',
                'area' => $area
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Error al actualizar jefe de personal',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * @OA\Delete(
     *     path="/api/jefe-personal/{id}",
     *     summary="Eliminar jefe de personal",
     *     tags={"Jefe Personal"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID del área del jefe de personal",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Jefe de personal eliminado correctamente",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Jefe de personal eliminado correctamente")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Jefe de personal no encontrado"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Error interno del servidor"
     *     )
     * )
     */
    public function destroy(string $id): JsonResponse
    {
        try {
            $area = Area::find($id);
            if (!$area) {
                return response()->json([
                    'error' => 'Jefe de personal no encontrado'
                ], 404);
            }

            $area->idJefe = null;
            $area->save();

            return response()->json([
                'message' => 'Jefe de personal eliminado correctamente'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Error al eliminar jefe de personal',
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
