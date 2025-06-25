<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\Contrato;
use App\Models\Hojasvida;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class contratoController extends Controller
{
    // En el método index, agregamos 'cargoArea' en el select para que lo traiga
    public function index()
    {
        $contratos = Contrato::with([
            'hojaDeVida:idHojaDeVida,usuarioNumDocumento',
            'hojaDeVida.usuario:numDocumento,primerNombre,primerApellido',
            'area:idArea,nombreArea',
            'tipoContrato:idTipoContrato,nomTipoContrato'
        ])->get([
            'idContrato',
            'tipoContratoId',
            'hojaDeVida',
            'area',
            'fechaIngreso',
            'fechaFinalizacion',
            'archivo',
            'estado',
            'cargoArea', // agregar aquí
        ]);

        return response()->json([
            'contratos' => $contratos,
            'status' => 200
        ]);
    }



    // En store agregar validación cargoArea y asignación
    public function store(Request $request)
    {
        $validated = $request->validate([
            'numDocumento' => 'required|integer',
            'tipoContratoId' => 'required|integer',
            'estado' => 'required|integer',
            'fechaIngreso' => 'required|date',
            'fechaFinalizacion' => 'required|date|after_or_equal:fechaIngreso',
            'archivo' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'area' => 'required|integer',
            'cargoArea' => 'required|integer|in:1,2',  // validamos cargoArea
        ]);

        $documento = $validated['numDocumento'];
        $hoja = Hojasvida::where('usuarioNumDocumento', $documento)->first();

        if (!$hoja) {
            return response()->json([
                'mensaje' => "Hoja de vida no encontrada para el documento $documento",
                'status' => 404
            ], 404);
        }

        if ($request->hasFile('archivo')) {
            $file = $request->file('archivo');
            $extension = $file->getClientOriginalExtension();
            $filename = time() . '_' . $documento . '.' . $extension;
            $folder = 'Archivos/' . $documento;
            $path = $file->storeAs($folder, $filename, 'public');
            $validated['archivo'] = 'storage/' . $path;
        }

        unset($validated['numDocumento']);
        $validated['hojaDeVida'] = $hoja->idHojaDeVida;


        $contrato = Contrato::create($validated);

        return response()->json([
            'mensaje' => 'Contrato creado correctamente',
            'contrato' => $contrato,
            'status' => 201
        ]);
    }




    public function show($id)
    {
        $contrato = Contrato::with('hojaDeVida')->find($id);

        if (!$contrato) {
            return response()->json([
                'mensaje' => 'Contrato no encontrado',
                'status' => 404
            ], 404);
        }

        return response()->json([
            'contrato' => $contrato,
            'status' => 200
        ]);
    }

    public function destroy($id)
    {
        $contrato = Contrato::find($id);

        if (!$contrato) {
            return response()->json([
                "mensaje" => "No se encontró el contrato",
                "status" => 404
            ], 404);
        }

        $contrato->delete();

        return response()->json([
            "mensaje" => "Contrato eliminado correctamente",
            "status" => 200
        ]);
    }

    // En update agregar validación cargoArea y asignación
    public function update(Request $request, $id)
    {
        $contrato = Contrato::find($id);

        if (!$contrato) {
            return response()->json([
                "mensaje" => "No se encontró el contrato",
                "status" => 404
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'tipoContratoId' => 'required|integer',
            'estado' => 'required|integer',
            'fechaIngreso' => 'required|date',
            'fechaFinalizacion' => 'required|date|after_or_equal:fechaIngreso',
            'archivo' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'area' => 'required|integer',
            'cargoArea' => 'required|integer|in:1,2',  // validamos cargoArea
        ]);

        if ($validator->fails()) {
            return response()->json([
                "errors" => $validator->errors(),
                "status" => 400
            ], 400);
        }

        $contrato->estado = $request->estado;
        $contrato->fechaIngreso = $request->fechaIngreso;
        $contrato->fechaFinalizacion = $request->fechaFinalizacion;
        $contrato->tipoContratoId = $request->tipoContratoId;
        $contrato->area = $request->area;
        $contrato->cargoArea = $request->cargoArea;  // asignamos cargoArea

        if ($request->hasFile('archivo')) {
            $documento = $request->input('numDocumento');
            if (!$documento) {
                return response()->json([
                    'mensaje' => 'Se requiere numDocumento para subir archivo',
                    'status' => 400
                ], 400);
            }
            $hoja = Hojasvida::where('usuarioNumDocumento', $documento)->first();
            if (!$hoja) {
                return response()->json([
                    'mensaje' => 'Hoja de vida no encontrada para el documento ' . $documento,
                    'status' => 404
                ], 404);
            }

            $file = $request->file('archivo');
            $extension = $file->getClientOriginalExtension();
            $filename = $documento . '.' . $extension;
            $folder = 'Archivos/' . $documento;
            $path = $file->storeAs($folder, $filename, 'public');

            $contrato->archivo = 'storage/' . $path;
        }

        $contrato->save();

        return response()->json([
            "mensaje" => "Contrato actualizado correctamente",
            "contrato" => $contrato,
            "status" => 200
        ]);
    }


    // En updatePartial igual agregar validación y asignación opcional de cargoArea
    public function updatePartial(Request $request, $id)
    {
        $contrato = Contrato::find($id);

        if (!$contrato) {
            return response()->json([
                "mensaje" => "No se encontró el contrato",
                "status" => 404
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'tipoContratoId' => 'nullable|integer',
            'estado' => 'nullable|integer',
            'fechaIngreso' => 'nullable|date',
            'fechaFinalizacion' => 'nullable|date',
            'area' => 'nullable|integer',
            'cargoArea' => 'nullable|integer|in:1,2',  // validamos cargoArea opcional
            'archivo' => 'nullable|file|max:5120'
        ]);

        if ($validator->fails()) {
            return response()->json([
                "mensaje" => "Error al validar el contrato",
                "errors" => $validator->errors(),
                "status" => 400
            ], 400);
        }

        if ($request->filled('estado'))
            $contrato->estado = $request->estado;
        if ($request->filled('fechaIngreso'))
            $contrato->fechaIngreso = $request->fechaIngreso;
        if ($request->filled('fechaFinalizacion'))
            $contrato->fechaFinalizacion = $request->fechaFinalizacion;
        if ($request->filled('tipoContratoId'))
            $contrato->tipoContratoId = $request->tipoContratoId;
        if ($request->filled('area'))
            $contrato->area = $request->area;
        if ($request->filled('cargoArea'))
            $contrato->cargoArea = $request->cargoArea;

        if ($request->hasFile('archivo')) {
            $documento = $request->input('numDocumento');
            if (!$documento) {
                return response()->json([
                    'mensaje' => 'Se requiere numDocumento para subir archivo',
                    'status' => 400
                ], 400);
            }
            $hoja = Hojasvida::where('usuarioNumDocumento', $documento)->first();
            if (!$hoja) {
                return response()->json([
                    'mensaje' => 'Hoja de vida no encontrada para el documento ' . $documento,
                    'status' => 404
                ], 404);
            }

            $file = $request->file('archivo');
            $extension = $file->getClientOriginalExtension();
            $filename = $documento . '.' . $extension;
            $folder = 'Archivos/' . $documento;
            $path = $file->storeAs($folder, $filename, 'public');

            $contrato->archivo = 'storage/' . $path;
        }

        $contrato->save();

        return response()->json([
            "mensaje" => "Contrato actualizado correctamente",
            "contrato" => $contrato,
            "status" => 200
        ]);
    }

    public function buscarPorDocumento($numDocumento)
    {
        $hoja = Hojasvida::where('usuarioNumDocumento', $numDocumento)->first();

        if (!$hoja) {
            return response()->json([
                'message' => 'Hoja de vida no encontrada',
                'contrato' => null
            ], 404);
        }

        $contrato = Contrato::with('hojaDeVida')
            ->where('hojaDeVida', $hoja->idHojaDeVida)
            ->first();

        if (!$contrato) {
            return response()->json([
                'message' => 'Contrato no encontrado',
                'contrato' => null
            ], 404);
        }

        return response()->json([
            'message' => 'Contrato encontrado',
            'contrato' => $contrato
        ], 200);
    }
}
