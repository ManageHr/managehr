<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Hojasvida;
use App\Models\Hojasvidahasexperiencia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class HojasvidahasexperienciaController extends Controller
{
    public function index()
    {
        $experiencias = Hojasvidahasexperiencia::all();
        return response()->json([
            "data" => $experiencias,
            "status" => 200
        ]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'idHojaDevida' => 'required|integer|exists:hojasvida,idHojaDeVida',
            'idExperiencia' => 'required|integer|exists:experiencialaboral,idExperiencia',
            'estado' => 'required|boolean',
            'archivo' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048'
        ]);

        if ($validator->fails()) {
            return response()->json([
                "mensaje" => "Error en la validación de la experiencia",
                "errors" => $validator->errors(),
                "status" => 400
            ], 400);
        }

        $data = $request->only(['idHojaDevida', 'idExperiencia', 'estado']); // ✅ ← ahora sí incluyes los datos

        if ($request->hasFile('archivo')) {
            $nombreLimpio = preg_replace('/[^A-Za-z0-9_\-]/', '_', $request->cargoEmpresa ?? 'experiencia');
            $extension = $request->file('archivo')->getClientOriginalExtension();
            $filename = $nombreLimpio . '_' . time() . '.' . $extension;
            $folder = 'Archivos/' . ($request->numDocumento ?? 'sin_documento');
            $path = $request->file('archivo')->storeAs($folder, $filename, 'public');
            $data['archivo'] = 'storage/' . $path;
        }

        try {
            $registro = Hojasvidahasexperiencia::create($data);
            return response()->json([
                "mensaje" => "Experiencia registrada correctamente",
                "experiencia" => $registro,
                "status" => 201
            ]);
        } catch (\Exception $e) {
            return response()->json([
                "mensaje" => "Error al registrar experiencia",
                "error" => $e->getMessage(),
                "status" => 500
            ], 500);
        }
    }


    public function show($id)
    {
        $exp = Hojasvidahasexperiencia::find($id);
        if (!$exp) {
            return response()->json([
                "mensaje" => "Experiencia no encontrada",
                "status" => 404
            ], 404);
        }

        return response()->json([
            "data" => $exp,
            "status" => 200
        ]);
    }

    public function update(Request $request, $id)
    {
        $exp = Hojasvidahasexperiencia::find($id);
        if (!$exp) {
            return response()->json([
                "mensaje" => "Experiencia no encontrada",
                "status" => 404
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'cargoEmpresa' => 'required|string|max:100',
            'empresa' => 'required|string|max:100',
            'tiempoExperiencia' => 'required|string|max:45',
            'estado' => 'required|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                "errors" => $validator->errors(),
                "status" => 400
            ], 400);
        }

        $exp->update($request->only(['cargoEmpresa', 'empresa', 'tiempoExperiencia', 'estado']));

        return response()->json([
            "mensaje" => "Experiencia actualizada",
            "data" => $exp,
            "status" => 200
        ]);
    }

    public function updatePartial(Request $request, $id)
    {
        $exp = Hojasvidahasexperiencia::find($id);
        if (!$exp) {
            return response()->json([
                "mensaje" => "Experiencia no encontrada",
                "status" => 404
            ], 404);
        }

        $exp->fill($request->only([
            'cargoEmpresa',
            'empresa',
            'tiempoExperiencia',
            'estado'
        ]));

        $exp->save();

        return response()->json([
            "mensaje" => "Experiencia actualizada parcialmente",
            "data" => $exp,
            "status" => 200
        ]);
    }

    public function destroy($id)
    {
        $exp = Hojasvidahasexperiencia::find($id);
        if (!$exp) {
            return response()->json([
                "mensaje" => "Experiencia no encontrada",
                "status" => 404
            ], 404);
        }

        $exp->delete();

        return response()->json([
            "mensaje" => "Experiencia eliminada",
            "status" => 200
        ]);
    }

    public function buscarPorDocumento($numDocumento)
    {
        $hoja = Hojasvida::where('usuarioNumDocumento', $numDocumento)->first();

        if (!$hoja) {
            return response()->json([
                "mensaje" => "Hoja de vida no encontrada",
                "status" => 404
            ], 404);
        }

        $experiencias = Hojasvidahasexperiencia::where('idHojaDeVida', $hoja->idHojaDeVida)->get();

        return response()->json([
            "data" => $experiencias,
            "status" => 200
        ]);
    }

    public function descargarArchivo($id)
    {
        $registro = Hojasvidahasexperiencia::find($id);

        if (!$registro || !$registro->archivo) {
            return response()->json([
                "mensaje" => "Archivo no encontrado",
                "status" => 404
            ], 404);
        }

        $ruta = storage_path('app/public/' . str_replace('storage/', '', $registro->archivo));

        if (!file_exists($ruta)) {
            return response()->json([
                "mensaje" => "El archivo no existe físicamente",
                "status" => 404
            ], 404);
        }

        return response()->download($ruta);
    }
}
