<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Area;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

/**
 * @OA\Tag(
 *     name="Área",
 *     description="Operaciones relacionadas con Áreas"
 * )
 */
class areaController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/areas",
     *     tags={"Área"},
     *     summary="Listar todas las áreas",
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Listado de áreas exitoso"
     *     )
     * )
     */
    public function index()
    {
        $areas = Area::all();
        return response()->json(["areas" => $areas, "status" => 200], 200);
    }
    /**
     * @OA\Post(
     *     path="/api/areas",
     *     tags={"Área"},
     *     summary="Crear nueva área",
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"nombreArea", "jefePersonal", "idJefe", "estado"},
     *             @OA\Property(property="nombreArea", type="string", example="Finanzas"),
     *             @OA\Property(property="jefePersonal", type="string", example="Carlos Pérez"),
     *             @OA\Property(property="idJefe", type="integer", example=3),
     *             @OA\Property(property="estado", type="integer", example=1)
     *         )
     *     ),
     *     @OA\Response(response=201, description="Área creada correctamente"),
     *     @OA\Response(response=400, description="Error de validación"),
     *     @OA\Response(response=500, description="Error interno")
     * )
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "nombreArea" => "required|string|max:50",
            "jefePersonal" => "required|string|max:100",
            "idJefe" => "required|integer",
            "estado" => "required|integer",
        ]);

        if ($validator->fails()) {
            return response()->json([
                "mensaje" => "Error en la validación del área",
                "errors" => $validator->errors(),
                "status" => 400
            ], 400);
        }

        try {
            $area = Area::create($request->all());
            return response()->json([
                "mensaje" => "Área creada correctamente",
                "area" => $area,
                "status" => 201
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                "mensaje" => "Error al crear el área",
                "error" => $e->getMessage(),
                "status" => 500
            ], 500);
        }
    }
    /**
     * @OA\Get(
     *     path="/api/areas/{id}",
     *     tags={"Área"},
     *     summary="Obtener área por ID",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID del área",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(response=200, description="Área encontrada"),
     *     @OA\Response(response=404, description="Área no encontrada")
     * )
     */
    public function show($id)
    {
        $area = Area::find($id);
        if (!$area) {
            return response()->json([
                "mensaje" => "No se encontró el área",
                "status" => 404
            ], 404);
        }
        return response()->json(["area" => $area, "status" => 200], 200);
    }
    /**
     * @OA\Get(
     *     path="/api/areas/nombre/{nombre}",
     *     tags={"Área"},
     *     summary="Buscar área por nombre",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="nombre",
     *         in="path",
     *         required=true,
     *         description="Nombre del área",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(response=200, description="Área encontrada"),
     *     @OA\Response(response=404, description="Área no encontrada")
     * )
     */
    public function showNombre($id)
    {
        $area = Area::where('nombreArea', $id)->get();
        
        if (!$area) {
            return response()->json([
                "mensaje" => "No se encontró el área",
                "status" => 404
            ], 404);
        }
        return response()->json(["area" => $area, "status" => 200], 200);
    }
    /**
     * @OA\Put(
     *     path="/api/areas/{id}",
     *     tags={"Área"},
     *     summary="Actualizar área completamente",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID del área a actualizar",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"nombreArea", "jefePersonal", "idJefe", "estado"},
     *             @OA\Property(property="nombreArea", type="string", example="Finanzas"),
     *             @OA\Property(property="jefePersonal", type="string", example="Carlos Pérez"),
     *             @OA\Property(property="idJefe", type="integer", example=3),
     *             @OA\Property(property="estado", type="integer", example=1)
     *         )
     *     ),
     *     @OA\Response(response=200, description="Área actualizada correctamente"),
     *     @OA\Response(response=400, description="Error de validación"),
     *     @OA\Response(response=404, description="Área no encontrada")
     * )
     */
    public function update(Request $request, $id)
    {
        $area = Area::find($id);
        if (!$area) {
            return response()->json([
                "mensaje" => "No se encontró el área",
                "status" => 404
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            "nombreArea" => "required|string|max:50",
            "jefePersonal" => "required|string|max:100",
            "idJefe" => "required|integer",
            "estado" => "required|integer",
            
        ]);

        if ($validator->fails()) {
            return response()->json([
                "mensaje" => "Error en la validación del área",
                "errors" => $validator->errors(),
                "status" => 400
            ], 400);
        }

        try {
            $area->update($request->all());
            return response()->json(["area" => $area, "status" => 200], 200);
        } catch (\Exception $e) {
            return response()->json([
                "mensaje" => "Error al actualizar el área",
                "error" => $e->getMessage(),
                "status" => 500
            ], 500);
        }
    }
    /**
     * @OA\Delete(
     *     path="/api/areas/{id}",
     *     tags={"Área"},
     *     summary="Eliminar un área",
     *     security={{"bearerAuth":{}}},
     *     description="Eliminar un área por su ID",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID del área a eliminar",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(response=200, description="Área eliminada correctamente"),
     *     @OA\Response(response=404, description="Área no encontrada")
     * )
     */
    public function destroy($id)
    {
        $area = Area::find($id);
        if (!$area) {
            return response()->json([
                "mensaje" => "No se encontró el área",
                "status" => 404
            ], 404);
        }

        $area->delete();
        return response()->json([
            "mensaje" => "Área eliminada correctamente",
            "status" => 200
        ], 200);
    }
    /**
     * @OA\Patch(
     *     path="/api/areas/{id}",
     *     tags={"Área"},
     *     summary="Actualizar parcialmente un área",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID del área",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         @OA\JsonContent(
     *             @OA\Property(property="nombreArea", type="string", example="Logística"),
     *             @OA\Property(property="jefePersonal", type="string", example="María León"),
     *             @OA\Property(property="idJefe", type="integer", example=5),
     *             @OA\Property(property="estado", type="integer", example=1)
     *         )
     *     ),
     *     @OA\Response(response=200, description="Área actualizada parcialmente"),
     *     @OA\Response(response=400, description="Error de validación"),
     *     @OA\Response(response=404, description="Área no encontrada")
     * )
     */
    public function updatePartial(Request $request, $id)
    {
        $area = Area::find($id);
        if (!$area) {
            $data = [
                "mensage" => " No se encontro Area",
                "status" => 404
            ];
            return response()->json([$data], 404);
        }
        $validator = Validator::make($request->all(), [
            "nombreArea" => "string|max:50",
            "jefePersonal" => "string|max:100",
            "idJefe" => "integer",
            "estado" => "integer",
        ]);
        if ($validator->fails()) {
            $data = [
                "mesaje " => "Error al validar genero",
                "errors" => $validator->errors(),
                "status" => 400
            ];
            return response()->json([$data], 400);
        }
        if ($request->has("estado")) {
            $area->estado = $request->estado;
        }
        if ($request->has("nombreArea")) {
            $area->nombreArea = $request->nombreArea;
        }
        if ($request->has("jefePersonal")) {
            $area->jefePersonal = $request->jefePersonal;
        }
        $area->save();
        $data = [
            "genero" => $area,
            "status" => 200
        ];
        return response()->json([$data], 200);
    }
    /**
 * @OA\Get(
 *     path="/api/jefepersonal/all",
 *     summary="Obtener todos los jefes de personal",
 *     description="Este endpoint retorna todos los usuarios que tienen el rol igual a 2 (jefes de personal).",
 *     operationId="obtenerJefes",
 *     tags={"Jefes de Personal"},
 *     @OA\Response(
 *         response=200,
 *         description="Lista de jefes obtenida exitosamente",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(
 *                 property="jefes",
 *                 type="array",
 *                 @OA\Items(
 *                     type="object",
 *                     @OA\Property(property="id", type="integer"),
 *                     @OA\Property(property="name", type="string"),
 *                     @OA\Property(property="email", type="string"),
 *                     @OA\Property(property="email_verified_at", type="string", nullable=true, format="date-time"),
 *                     @OA\Property(property="rol", type="integer"),
 *                     @OA\Property(property="created_at", type="string", format="date-time"),
 *                     @OA\Property(property="updated_at", type="string", format="date-time")
 *                 )
 *             ),
 *             @OA\Property(property="status", type="integer", example=200)
 *         )
 *     ),
 *     @OA\Response(
 *         response=500,
 *         description="Error interno del servidor"
 *     )
 * )
 */

    public function obtenerJefes()
    {
        $jefes = User::where('rol', 2)->get();  


        return response()->json([
            'jefes' => $jefes,
            'status' => 200
        ]);
    }
}
