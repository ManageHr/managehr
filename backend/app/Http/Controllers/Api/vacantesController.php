<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Vacantes;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;

class vacantesController extends Controller
{
    public function index()
    {
        $vacantes = Vacantes::all();
        return response()->json($vacantes, Response::HTTP_OK);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nomVacante' => 'required|string|max:30',
            'descripVacante' => 'nullable|string',
            'salario' => 'nullable|numeric',
            'expMinima' => 'nullable|string|max:45',
            'cargoVacante' => 'nullable|string|max:45',
            'catVacId' => 'nullable|integer',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Error de validación',
                'errors' => $validator->errors()
            ], Response::HTTP_BAD_REQUEST);
        }

        try {
            $vacante = Vacantes::create($request->all());
            return response()->json($vacante, Response::HTTP_CREATED);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error interno del servidor al crear la vacante',
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function show($id)
    {
        $vacante = Vacantes::find($id);

        if ($vacante) {
            return response()->json($vacante, Response::HTTP_OK);
        } else {
            return response()->json(['message' => 'Vacante no encontrada'], Response::HTTP_NOT_FOUND);
        }
    }

    public function update(Request $request, $id)
    {
        $vacante = Vacantes::find($id);

        if (!$vacante) {
            return response()->json(['message' => 'Vacante no encontrada'], Response::HTTP_NOT_FOUND);
        }

        $validator = Validator::make($request->all(), [
            'nomVacante' => 'required|string|max:30',
            'descripVacante' => 'nullable|string',
            'salario' => 'nullable|numeric',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Error de validación',
                'errors' => $validator->errors()
            ], Response::HTTP_BAD_REQUEST);
        }

        try {
            $vacante->update($request->all());
            return response()->json($vacante, Response::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error interno del servidor al actualizar la vacante',
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function destroy($id)
    {
        $vacante = Vacantes::find($id);

        if (!$vacante) {
            return response()->json(['message' => 'Vacante no encontrada'], Response::HTTP_NOT_FOUND);
        }

        try {
            $vacante->delete();
            return response()->json(null, Response::HTTP_NO_CONTENT);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error interno del servidor al eliminar la vacante',
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
