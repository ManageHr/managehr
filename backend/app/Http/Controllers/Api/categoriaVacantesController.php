<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\CategoriaVacantes;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Log;

/**
 * @OA\Tag(
 *     name="Categorías de Vacantes",
 *     description="Gestión de categorías para vacantes laborales"
 * )
 */
class CategoriaVacantesController extends Controller
{
     /**
     * @OA\Get(
     *     path="/api/categoriavacantes",
     *     tags={"Categorías de Vacantes"},
     *     summary="Listar todas las categorías de vacantes",
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Listado obtenido correctamente"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Error interno del servidor"
     *     )
     * )
     */
    public function index()
    {
        try {
            $categorias = CategoriaVacantes::all();

            return response()->json([
                'categoriavacantes' => $categorias
            ], 200);

        } catch (\Exception $e) {
            Log::error('Error al obtener categorías de vacantes (Api\CategoriaVacantesController::index): ' . $e->getMessage());
            return response()->json(['message' => 'Ocurrió un error al obtener las categorías.', 'error' => $e->getMessage()], 500);
        }
    }
     /**
     * @OA\Post(
     *     path="/api/categoriavacantes",
     *     tags={"Categorías de Vacantes"},
     *     summary="Crear una nueva categoría de vacante",
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"nomCategoria"},
     *             @OA\Property(property="nomCategoria", type="string", maxLength=45, example="Tecnología")
     *         )
     *     ),
     *     @OA\Response(response=201, description="Categoría creada exitosamente"),
     *     @OA\Response(response=400, description="Error de validación"),
     *     @OA\Response(response=500, description="Error interno del servidor")
     * )
     */

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nomCategoria' => 'required|string|max:45|unique:categoriavacantes,nomCategoria',
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => 'Error de validación', 'errors' => $validator->errors()], 400);
        }

        try {
            $categoria = new CategoriaVacantes();
            $categoria->nomCategoria = $request->nomCategoria;
            $categoria->save();

            return response()->json([
                'message' => 'Categoría creada con éxito',
                'categoria' => $categoria
            ], 201);

        } catch (\Exception $e) {
             Log::error('Error al crear categoría de vacantes (Api\CategoriaVacantesController::store): ' . $e->getMessage());
            return response()->json(['message' => 'Ocurrió un error al crear la categoría.', 'error' => $e->getMessage()], 500);
        }
    }
    /**
     * @OA\Get(
     *     path="/api/categoriavacantes/{id}",
     *     tags={"Categorías de Vacantes"},
     *     summary="Obtener una categoría por su ID",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID de la categoría",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(response=200, description="Categoría encontrada"),
     *     @OA\Response(response=404, description="Categoría no encontrada"),
     *     @OA\Response(response=500, description="Error interno del servidor")
     * )
     */
    public function show($id)
    {
        try {
            $categoria = CategoriaVacantes::find($id);

            if (!$categoria) {
                return response()->json(['message' => 'Categoría no encontrada'], 404);
            }

            return response()->json($categoria, 200);

        } catch (\Exception $e) {
             Log::error('Error al obtener categoría de vacantes con ID ' . $id . ' (Api\CategoriaVacantesController::show): ' . $e->getMessage());
            return response()->json(['message' => 'Ocurrió un error al obtener la categoría.', 'error' => $e->getMessage()], 500);
        }
    }
    /**
     * @OA\Put(
     *     path="/api/categoriavacantes/{id}",
     *     tags={"Categorías de Vacantes"},
     *     summary="Actualizar una categoría existente",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID de la categoría a actualizar",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"nomCategoria"},
     *             @OA\Property(property="nomCategoria", type="string", maxLength=45, example="Administración")
     *         )
     *     ),
     *     @OA\Response(response=200, description="Categoría actualizada con éxito"),
     *     @OA\Response(response=400, description="Error de validación"),
     *     @OA\Response(response=404, description="Categoría no encontrada"),
     *     @OA\Response(response=500, description="Error interno del servidor")
     * )
     */
    public function update(Request $request, $id)
    {
        $categoria = CategoriaVacantes::find($id);

        if (!$categoria) {
            return response()->json(['message' => 'Categoría no encontrada'], 404);
        }

        $validator = Validator::make($request->all(), [
            'nomCategoria' => [
                'required',
                'string',
                'max:45',
                Rule::unique('categoriavacantes', 'nomCategoria')->ignore($categoria->idCatVac, 'idCatVac'),
            ],
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => 'Error de validación', 'errors' => $validator->errors()], 400);
        }

        try {
            $categoria->nomCategoria = $request->nomCategoria;
            $categoria->save();

            return response()->json(['message' => 'Categoría actualizada con éxito', 'categoria' => $categoria], 200);

        } catch (\Exception $e) {
            Log::error('Error al actualizar categoría de vacantes con ID ' . $id . ' (Api\CategoriaVacantesController::update): ' . $e->getMessage());
            return response()->json(['message' => 'Ocurrió un error al actualizar la categoría.', 'error' => $e->getMessage()], 500);
        }
    }
    /**
     * @OA\Delete(
     *     path="/api/categoriavacantes/{id}",
     *     tags={"Categorías de Vacantes"},
     *     summary="Eliminar una categoría de vacante",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID de la categoría",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(response=200, description="Categoría eliminada con éxito"),
     *     @OA\Response(response=404, description="Categoría no encontrada"),
     *     @OA\Response(response=409, description="No se puede eliminar la categoría si está asociada a vacantes"),
     *     @OA\Response(response=500, description="Error interno del servidor")
     * )
     */
    public function destroy($id)
    {
        $categoria = CategoriaVacantes::find($id);

        if (!$categoria) {
            return response()->json(['message' => 'Categoría no encontrada'], 404);
        }

        try {
            $categoria->delete();

            return response()->json(['message' => 'Categoría eliminada con éxito'], 200);

        } catch (\Exception $e) {
            Log::error('Error al eliminar categoría de vacantes con ID ' . $id . ' (Api\CategoriaVacantesController::destroy): ' . $e->getMessage());
             if ($e instanceof \Illuminate\Database\QueryException && $e->getCode() === '23000') {
                 return response()->json(['message' => 'No se puede eliminar la categoría porque está asociada a vacantes existentes.'], 409);
             }
            return response()->json(['message' => 'Ocurrió un error al eliminar la categoría.', 'error' => $e->getMessage()], 500);
        }
    }

   
}