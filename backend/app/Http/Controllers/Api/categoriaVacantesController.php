<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\CategoriaVacantes;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Log;

class CategoriaVacantesController extends Controller
{
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