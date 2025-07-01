<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\Estudios;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class estudiosController extends Controller
{

    public function index()
    {
        $estudios = Estudios::all();
        return response()->json([
            "estudios" => $estudios,
            "status" => 200
        ]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nomEstudio' => 'required|string|max:45',
            'nomInstitucion' => 'required|string|max:50',
            'tituloObtenido' => 'required|string|max:45',
            'anioInicio' => 'required|date:',
            'anioFinalizacion' => 'required|date'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'mensaje' => 'Error en la validación de datos del estudio',
                'errors' => $validator->errors(),
                'status' => 400
            ], 400);
        }

        try {
            // Convertir año a fecha válida (ejemplo: 2025 → 2025-01-01)
            $data = $request->all();
            $data['anioFinalizacion'] = $request->anioFinalizacion . '-01-01';

            $estudio = Estudios::create($data);

            return response()->json([
                'mensaje' => 'Estudio creado correctamente',
                'estudio' => $estudio,
                'status' => 201
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'mensaje' => 'Error al crear el estudio',
                'error' => $e->getMessage(),
                'status' => 500
            ], 500);
        }
    }


    public function show($id)
    {
        $estudio = Estudios::find($id);
        if (!$estudio) {
            return response()->json([
                'mensaje' => 'Estudio no encontrado',
                'status' => 404
            ], 404);
        }

        return response()->json([
            'estudio' => $estudio,
            'status' => 200
        ]);
    }

    public function update(Request $request, $id)
    {
        $estudio = Estudios::find($id);
        if (!$estudio) {
            return response()->json([
                'mensaje' => 'Estudio no encontrado',
                'status' => 404
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'nomEstudio' => 'required|string|max:100',
            'nomInstitucion' => 'required|string|max:100',
            'tituloObtenido' => 'required|string|max:100',
            'anioInicio' => 'required|date',
            'anioFinalizacion' => 'required|date'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'mensaje' => 'Error de validación',
                'errors' => $validator->errors(),
                'status' => 400
            ], 400);
        }

        try {
            $estudio->update($request->all());
            return response()->json([
                'mensaje' => 'Estudio actualizado correctamente',
                'estudio' => $estudio,
                'status' => 200
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'mensaje' => 'Error al actualizar el estudio',
                'error' => $e->getMessage(),
                'status' => 500
            ], 500);
        }
    }

    public function destroy($id)
    {
        $estudio = Estudios::find($id);
        if (!$estudio) {
            return response()->json([
                'mensaje' => 'Estudio no encontrado',
                'status' => 404
            ], 404);
        }

        $estudio->delete();
        return response()->json([
            'mensaje' => 'Estudio eliminado correctamente',
            'status' => 200
        ]);
    }

    public function updatePartial(Request $request, $id)
    {
        $estudio = Estudios::find($id);
        if (!$estudio) {
            return response()->json([
                'mensaje' => 'Estudio no encontrado',
                'status' => 404
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'nomEstudio' => 'string|max:100',
            'nomInstitucion' => 'string|max:100',
            'tituloObtenido' => 'string|max:100',
            "anioInicio" => "date",
            'anioFinalizacion' => 'date'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'mensaje' => 'Error de validación',
                'errors' => $validator->errors(),
                'status' => 400
            ], 400);
        }

        $estudio->fill($request->all());
        $estudio->save();

        return response()->json([
            'mensaje' => 'Estudio actualizado parcialmente',
            'estudio' => $estudio,
            'status' => 200
        ]);
    }
    public function buscarPorCampos(Request $request)
    {
        $request->validate([
            'nomEstudio' => 'required|string',
            'nomInstitucion' => 'required|string',
            'tituloObtenido' => 'required|string',
        ]);

        $estudio = Estudios::where('nomEstudio', $request->nomEstudio)
            ->where('nomInstitucion', $request->nomInstitucion)
            ->where('tituloObtenido', $request->tituloObtenido)
            ->first();

        if ($estudio) {
            return response()->json(['existe' => true, 'estudio' => $estudio], 200);
        } else {
            return response()->json(['existe' => false], 200);
        }
    }
}
