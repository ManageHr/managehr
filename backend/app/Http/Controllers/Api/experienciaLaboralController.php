<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\ExperienciaLaboral;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class experienciaLaboralController extends Controller
{
    public function index()
    {
        $experiencias = ExperienciaLaboral::all();
        return response()->json([
            "experiencias" => $experiencias,
            "status" => 200
        ]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nomEmpresa' => 'required|string|max:45',
            'nomJefe' => 'required|string|max:45',
            'telefono' => 'required|integer',
            'cargo' => 'required|string|max:20',
            'actividades' => 'required|string',
            'fechaInicio' => 'required|date',
            'fechaFinalizacion' => 'required|date|after_or_equal:fechaInicio'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'mensaje' => 'Error en la validación de datos de experiencia',
                'errors' => $validator->errors(),
                'status' => 400
            ], 400);
        }

        try {
            $experiencia = ExperienciaLaboral::create($request->all());
            return response()->json([
                'mensaje' => 'Experiencia registrada correctamente',
                'experiencia' => $experiencia,
                'status' => 201
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'mensaje' => 'Error al registrar la experiencia',
                'error' => $e->getMessage(),
                'status' => 500
            ], 500);
        }
    }

    public function show($id)
    {
        $experiencia = ExperienciaLaboral::find($id);
        if (!$experiencia) {
            return response()->json([
                'mensaje' => 'Experiencia no encontrada',
                'status' => 404
            ]);
        }

        return response()->json([
            'experiencia' => $experiencia,
            'status' => 200
        ]);
    }

    public function update(Request $request, $id)
    {
        $experiencia = ExperienciaLaboral::find($id);
        if (!$experiencia) {
            return response()->json([
                'mensaje' => 'Experiencia no encontrada',
                'status' => 404
            ]);
        }

        $validator = Validator::make($request->all(), [
            'nomEmpresa' => 'required|string|max:45',
            'nombJefe' => 'required|string|max:45',
            'telefono' => 'required|numeric|digits_between:7,11',
            'cargo' => 'required|string|max:20',
            'actividades' => 'required|string',
            'fechaInicio' => 'required|date',
            'fechaFinalizacion' => 'required|date|after_or_equal:fechaInicio'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'mensaje' => 'Error en la validación',
                'errors' => $validator->errors(),
                'status' => 400
            ]);
        }

        $experiencia->update($request->all());

        return response()->json([
            'mensaje' => 'Experiencia actualizada correctamente',
            'experiencia' => $experiencia,
            'status' => 200
        ]);
    }

    public function destroy($id)
    {
        $experiencia = ExperienciaLaboral::find($id);
        if (!$experiencia) {
            return response()->json([
                'mensaje' => 'Experiencia no encontrada',
                'status' => 404
            ]);
        }

        $experiencia->delete();
        return response()->json([
            'mensaje' => 'Experiencia eliminada correctamente',
            'status' => 200
        ]);
    }

    public function updatePartial(Request $request, $id)
    {
        $experiencia = ExperienciaLaboral::find($id);
        if (!$experiencia) {
            return response()->json([
                'mensaje' => 'Experiencia no encontrada',
                'status' => 404
            ]);
        }

        $validator = Validator::make($request->all(), [
            'nomEmpresa' => 'string|max:45',
            'nombJefe' => 'string|max:45',
            'telefono' => 'numeric|digits_between:7,11',
            'cargo' => 'string|max:20',
            'actividades' => 'string',
            'fechaInicio' => 'date',
            'fechaFinalizacion' => 'date|after_or_equal:fechaInicio'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'mensaje' => 'Error en la validación parcial',
                'errors' => $validator->errors(),
                'status' => 400
            ]);
        }

        $experiencia->fill($request->all());
        $experiencia->save();

        return response()->json([
            'mensaje' => 'Experiencia actualizada parcialmente',
            'experiencia' => $experiencia,
            'status' => 200
        ]);
    }
}
