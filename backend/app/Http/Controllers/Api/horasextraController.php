<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Horasextra;
use Illuminate\Support\Facades\Validator;

class HorasextraController extends Controller
{
    public function index()
    {
        $horas = Horasextra::all();
        return response()->json([
            'horasextra' => $horas,
            'status' => 200
        ], 200);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'descrip' => 'required|string|max:500',
            'fecha' => 'required|date',
            'nHorasExtra' => 'required|numeric|min:0',
            'tipoHorasid' => 'required|integer',
            'contratoId' => 'required|integer'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'mensaje' => 'Error en la validación de datos de horas extra:',
                'errors' => $validator->errors(),
                'status' => 400
            ], 400);
        }

        try {
            $horas = Horasextra::create($request->all());

            return response()->json([
                'mensaje' => 'Horas extra creada correctamente',
                'horasextra' => $horas,
                'status' => 201
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'mensaje' => 'Error al crear horas extra:',
                'error' => $e->getMessage(),
                'status' => 500
            ], 500);
        }
    }

    public function show($id)
    {
        $horas = Horasextra::find($id);

        if (!$horas) {
            return response()->json([
                'mensaje' => 'Horas extra no encontrada',
                'status' => 404
            ], 404);
        }

        return response()->json([
            'horasextra' => $horas,
            'status' => 200
        ], 200);
    }

    public function update(Request $request, $id)
    {
        $horas = Horasextra::find($id);

        if (!$horas) {
            return response()->json([
                'mensaje' => 'Horas extra no encontrada',
                'status' => 404
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'descrip' => 'required|string|max:500',
            'fecha' => 'required|date',
            'nHorasExtra' => 'required|numeric|min:0',
            'tipoHorasid' => 'required|integer',
            'contratoId' => 'required|integer'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'mensaje' => 'Error en la validación',
                'errors' => $validator->errors(),
                'status' => 400
            ], 400);
        }

        $horas->update($request->all());

        return response()->json([
            'mensaje' => 'Horas extra actualizada correctamente',
            'horasextra' => $horas,
            'status' => 200
        ]);
    }

    public function updatePartial(Request $request, $id)
    {
        $horas = Horasextra::find($id);

        if (!$horas) {
            return response()->json([
                'mensaje' => 'Horas extra no encontrada',
                'status' => 404
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'descrip' => 'string|max:500',
            'fecha' => 'date',
            'nHorasExtra' => 'numeric|min:0',
            'tipoHorasid' => 'integer',
            'contratoId' => 'integer'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'mensaje' => 'Error en la validación',
                'errors' => $validator->errors(),
                'status' => 400
            ], 400);
        }

        $horas->update($request->only([
            'descrip',
            'fecha',
            'nHorasExtra',
            'tipoHorasid',
            'contratoId'
        ]));

        return response()->json([
            'mensaje' => 'Horas extra actualizada parcialmente',
            'horasextra' => $horas,
            'status' => 200
        ]);
    }

    public function destroy($id)
    {
        $horas = Horasextra::find($id);

        if (!$horas) {
            return response()->json([
                'mensaje' => 'Horas extra no encontrada',
                'status' => 404
            ], 404);
        }

        $horas->delete();

        return response()->json([
            'mensaje' => 'Horas extra eliminada correctamente',
            'status' => 200
        ]);
    }
}
