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
        'archivo' => 'nullable|string|max:255' // Se permite nulo
    ]);

    if ($validator->fails()) {
        return response()->json([
            'mensaje' => 'Error en la validación de la experiencia',
            'errors' => $validator->errors(),
            'status' => 400
        ], 400);
    }

    try {
        // Si no viene el campo 'archivo', se define como null
        $data = $request->all();
        $data['archivo'] = $data['archivo'] ?? null;

        $relacion = Hojasvidahasexperiencia::create($data);

        return response()->json([
            'mensaje' => 'Relación experiencia creada correctamente',
            'data' => $relacion,
            'status' => 201
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'mensaje' => 'Error al guardar la relación experiencia',
            'error' => $e->getMessage(),
            'status' => 500
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
    $relacion = Hojasvidahasexperiencia::find($id);
    if (!$relacion) {
        return response()->json([
            'mensaje' => 'Relación no encontrada',
            'status' => 404
        ]);
    }

    $relacion->delete();

    return response()->json([
        'mensaje' => 'Relación experiencia eliminada correctamente',
        'status' => 200
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

        $experiencias = Hojasvidahasexperiencia::where('idHojaDevida', $hoja->idHojaDeVida)->get();

        return response()->json([
            "data" => $experiencias,
            "status" => 200
        ]);
    }

    public function buscarPorHojaDeVida($idHojaDevida)
    {
        $experiencias = Hojasvidahasexperiencia::where('idHojaDevida', $idHojaDevida)->get();

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

    public function buscarPorHojaId($idHojaDeVida)
{
    try {
        $relaciones = Hojasvidahasexperiencia::with('experiencia')
            ->where('idHojaDevida', $idHojaDeVida)
            ->get();

        return response()->json([
            'data' => $relaciones,
            'status' => 200
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'mensaje' => 'Error al obtener experiencias de la hoja de vida',
            'error' => $e->getMessage(),
            'status' => 500
        ], 500);
    }
}

}
