<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\Contrato;
use App\Models\Hojasvida;
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

    public function store(Request $request)
    {
        $validated = $request->validate([
            'numDocumento' => 'required|integer',
            'tipoContratoId' => 'required|integer',
            'estado' => 'required|integer',
            'fechaIngreso' => 'required|date',
            'fechaFinal' => 'required|date',
            'documento' => 'nullable|file|max:5120',
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

        $contrato = Contrato::create($validated);

        return response()->json([
            'mensaje' => 'Contrato creado correctamente',
            'contrato' => $contrato,
            'status' => 201
        ]);
    }

    public function show($id)
    {
        $contrato = Contrato::find($id);
        return response()->json([
            "contrato" => $contrato,
            "status" => 200
        ], 200);
    }

    public function destroy($id)
    {
        $contrato = Contrato::find($id);
        if (!$contrato) {
            return response()->json([
                "mensage" => " No se encontro el contrato",
                "status" => 404
            ], 404);
        }
        $contrato->delete();
        return response()->json([
            "contrato" => 'Contrato eliminado',
            "status" => 200
        ], 200);
    }

    public function update(Request $request, $id)
    {
        $contrato = Contrato::find($id);
        if (!$contrato) {
            return response()->json([
                "mensage" => " No se encontro el contrato",
                "status" => 404
            ], 404);
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
            return response()->json([
                "errors" => $validator->errors(),
                "status" => 400
            ], 400);
        }

        $contrato->estado = $request->estado;
        $contrato->fechaIngreso = $request->fechaIngreso;
        $contrato->fechaFinal = $request->fechaFinal;
        $contrato->documento = $request->documento;
        $contrato->tipoContratoId = $request->tipoContratoId;
        $contrato->numDocumento = $request->numDocumento;

        try {
            $contrato->save();
            return response()->json([
                "contrato" => $contrato,
                "status" => 200
            ], 200);
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

        if ($request->has("estado")) $contrato->estado = $request->estado;
        if ($request->has("fechaIngreso")) $contrato->fechaIngreso = $request->fechaIngreso;
        if ($request->has("fechaFinal")) $contrato->fechaFinal = $request->fechaFinal;
        if ($request->has("tipoContratoId")) $contrato->tipoContratoId = $request->tipoContratoId;
        if ($request->has("numDocumento")) $contrato->numDocumento = $request->numDocumento;
        if ($request->has("areaId")) $contrato->areaId = $request->areaId;

        if ($request->hasFile('documento')) {
            $file = $request->file('documento');
            $numDocumento = $request->input('numDocumento');
            $folder = 'Archivos/' . $numDocumento;

            $extension = $file->getClientOriginalExtension();
            $filename = $numDocumento . '.' . $extension;

            $path = $file->storeAs($folder, $filename, 'public');
            $contrato->documento = 'storage/' . $path;
        }

        $contrato->save();

        return response()->json([
            "mensaje" => "Contrato actualizado correctamente",
            "contrato" => $contrato,
            "status" => 200
        ], 200);
    }

    /**
     * Obtener contrato por número de documento de usuario (para solicitudes de vacaciones).
     */
    public function buscarPorDocumento($numDocumento)
{
    $hoja = Hojasvida::where('usuarioNumDocumento', $numDocumento)->first();

    if (!$hoja) {
        return response()->json([
            'message' => 'Hoja de vida no encontrada',
            'contrato' => null
        ], 200);
    }

    $contrato = Contrato::where('hojaDeVida', $hoja->idHojaDeVida)->first();

    if (!$contrato) {
        return response()->json([
            'message' => 'Contrato no encontrado',
            'contrato' => null
        ], 200);
    }

    return response()->json([
        'message' => 'Contrato encontrado',
        'contrato' => $contrato
    ], 200);
}

}
