<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Vacaciones;
use Illuminate\Support\Facades\Validator;

class vacacioneController extends Controller
{
    public function index()
    {
        $vacaciones = Vacaciones::all();
        return response()->json([
            "vacaciones" => $vacaciones,
            "status" => 200
        ]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'motivo' => 'required|string|max:500',
            'fechaInicio' => 'required|date',
            'fechaFinal' => 'required|date',
            'dias' => 'required|integer',
            'contratoId' => 'required|integer'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'mensaje' => 'Error en la validación de datos',
                'errors' => $validator->errors(),
                'status' => 400
            ], 400);
        }

        try {
            $vacaciones = Vacaciones::create($request->only([
                'motivo',
                'fechaInicio',
                'fechaFinal',
                'dias',
                'contratoId'
            ]));

            return response()->json([
                'mensaje' => 'Vacación creada correctamente',
                'vacaciones' => $vacaciones,
                'status' => 201
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'mensaje' => 'Error al crear la vacación',
                'error' => $e->getMessage(),
                'status' => 500
            ], 500);
        }
    }
    public function show($id)
    {
        $vacaciones = Vacaciones::find($id);
        if (!$vacaciones) {
            return response()->json([
                "mensaje" => "Vacación no encontrada",
                "status" => 404
            ], 404);
        }

        return response()->json([
            "vacaciones" => $vacaciones,
            "status" => 200
        ]);
    }

    public function update(Request $request, $id)
    {
        $vacaciones = Vacaciones::find($id);
        if (!$vacaciones) {
            return response()->json([
                "mensaje" => "Vacación no encontrada",
                "status" => 404
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'motivo' => 'required|string|max:500',
            'fechaInicio' => 'required|date',
            'fechaFinal' => 'required|date',
            'dias' => 'required|integer',
            'contratoId' => 'required|integer'
        ]);

        if ($validator->fails()) {
            return response()->json([
                "errors" => $validator->errors(),
                "status" => 400
            ], 400);
        }

        $vacaciones->update($request->only([
            'motivo',
            'fechaInicio',
            'fechaFinal',
            'dias',
            'contratoId'
        ]));

        return response()->json([
            "vacaciones" => $vacaciones,
            "status" => 200
        ]);
    }

    public function updatePartial(Request $request, $id)
    {
        $vacaciones = Vacaciones::find($id);
        if (!$vacaciones) {
            return response()->json([
                "mensaje" => "Vacación no encontrada",
                "status" => 404
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'motivo' => 'sometimes|string|max:500',
            'fechaInicio' => 'sometimes|date',
            'fechaFinal' => 'sometimes|date',
            'dias' => 'sometimes|integer',
            'contratoId' => 'sometimes|integer'
        ]);

        if ($validator->fails()) {
            return response()->json([
                "mensaje" => "Error de validación",
                "errors" => $validator->errors(),
                "status" => 400
            ], 400);
        }

        $vacaciones->update($request->only([
            'motivo',
            'fechaInicio',
            'fechaFinal',
            'dias',
            'contratoId'
        ]));

        return response()->json([
            "vacaciones" => $vacaciones,
            "status" => 200
        ]);
    }

    public function destroy($id)
    {
        $vacaciones = Vacaciones::find($id);
        if (!$vacaciones) {
            return response()->json([
                "mensaje" => "Vacación no encontrada",
                "status" => 404
            ], 404);
        }

        $vacaciones->delete();

        return response()->json([
            "mensaje" => "Vacación eliminada",
            "status" => 200
        ]);
    }
}
