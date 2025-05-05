<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\Contrato;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class contratoController extends Controller
{

    public function index()
    {
        $contrato = Contrato::all();
        $data = [
            "contrato" => $contrato,
            "status" => 200
        ];
        return response()->json($data, 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'numDocumento' => 'required|integer',
            'tipoContratoId' => 'required|integer',
            'estado' => 'required|integer',
            'fechaIngreso' => 'required|date',
            'fechaFinal' => 'required|date',
            'documento' => 'nullable|file|max:5120', // 5MB max
            'areaId' => 'required| integer'
        ]);

       
        if ($request->hasFile('documento')) {
            $file = $request->file('documento');
            $folder = 'Archivos/' . $request->input('numDocumento');

            
            $extension = $file->getClientOriginalExtension();
            $filename = $request->input('numDocumento') . '.' . $extension;
            $path = $file->storeAs($folder, $filename, 'public');

            
            $validated['documento'] = 'storage/' . $path;
        }

        // Guardar contrato
        $contrato = Contrato::create($validated);

        return response()->json([
            'mensaje' => 'Contrato creado correctamente',
            'contrato' => $contrato,
            'status' => 201
        ]);
    }


    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $contrato = Contrato::find($id);
        $data = [
            "contrato" => $contrato,
            "status" => 200
        ];
        return response()->json($data, 200);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function destroy($id)
    {
        $contrato = Contrato::find($id);
        if (!$contrato) {
            $data = [
                "mensage" => " No se encontro el contrato",
                "status" => 404
            ];
            return response()->json([$data], 404);
        }
        $contrato->delete();
        $data = [
            "contrato:" => 'Contrato eliminado',
            "status" => 200
        ];
        return response()->json([$data], 200);
    }
    public function update(Request $request, $id)
    {
        $contrato = Contrato::find($id);
        if (!$contrato) {
            $data = [
                "mensage" => " No se encontro el contrato",
                "status" => 404
            ];
            return response()->json([$data], 404);
        }
        $validator = Validator::make($request->all(), [
            'estado' => 'required|integer',
            'fechaIngreso' => 'required|date',
            'fechaFinal' => 'required|date',
            'documento' => 'required|string|max:100',
            'tipoContratoId' => 'required',
            'numDocumento' => 'required',
            'areaId' => 'required| integer'
        ]);
        if ($validator->fails()) {
            $data = [
                "errors" => $validator->errors(),
                "status" => 400
            ];
            return response()->json([$data], 400);
        }

        $contrato->estado = $request->estado;
        $contrato->fechaIngreso = $request->fechaIngreso;
        $contrato->fechaFinal = $request->fechaFinal;
        $contrato->documento = $request->documento;
        $contrato->tipoContratoId = $request->tipoContratoId;
        $contrato->numDocumento = $request->numDocumento;

        try {
            $contrato->save();
            $data = [
                "contrato" => $contrato,
                "status" => 200
            ];
            return response()->json([$data], 200);
        } catch (\Exception $e) {
            return response()->json([
                "mensaje" => "Error al modificar el contrato",
                "error" => $e->getMessage(),
                "status" => 500
            ], 500);
        }
    }
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
            'numDocumento' => 'nullable|integer',
            'tipoContratoId' => 'nullable|integer',
            'estado' => 'nullable|integer',
            'fechaIngreso' => 'nullable|date',
            'fechaFinal' => 'nullable|date',
            'documento' => 'nullable|file|max:5120',
            'areaId' => 'nullable| integer'
        ]);

        if ($validator->fails()) {
            return response()->json([
                "mensaje" => "Error al validar el contrato",
                "errors" => $validator->errors(),
                "status" => 400
            ], 400);
        }

        // Solo actualiza si se recibe cada campo
        if ($request->has("estado")) {
            $contrato->estado = $request->estado;
        }
    
        if ($request->has("fechaIngreso")) {
            $contrato->fechaIngreso = $request->fechaIngreso;
        }
    
        if ($request->has("fechaFinal")) {
            $contrato->fechaFinal = $request->fechaFinal;
        }
    
        if ($request->has("tipoContratoId")) {
            $contrato->tipoContratoId = $request->tipoContratoId;
        }
    
        if ($request->has("numDocumento")) {
            $contrato->numDocumento = $request->numDocumento;
        }
        if ($request->has("areaId")) {
            $contrato->areaId = $request->areaId;
        }
    
        // Documento (archivo)
        if ($request->hasFile('documento')) {
            $file = $request->file('documento');
            $numDocumento = $request->input('numDocumento');
            $folder = 'Archivos/' . $numDocumento;
    
            $extension = $file->getClientOriginalExtension();
            $filename = $numDocumento . '.' . $extension;
    
            $path = $file->storeAs($folder, $filename, 'public');
    
            // ✅ Actualiza la ruta en la base de datos
            $contrato->documento = 'storage/' . $path;
        }
        
        $contrato->save();
    
        return response()->json([
            "mensaje" => "Contrato actualizado correctamente",
            "contrato" => $contrato,
            "status" => 200
        ], 200);
    }
}
