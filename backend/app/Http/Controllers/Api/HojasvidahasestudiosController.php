<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Hojasvida;
use App\Models\Estudios;
use Illuminate\Http\Request;
use App\Models\Hojasvidahasestudios;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;

class HojasvidahasestudiosController extends Controller
{
    public function index()
    {
        $registros = Hojasvidahasestudios::all();

        return response()->json([
            "data" => $registros,
            "status" => 200
        ], 200);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'numDocumento' => 'required|integer',
            'idEstudios' => 'required|exists:estudios,idEstudios',
            'estado' => 'required|boolean',
            'archivo' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
        ]);


        if ($validator->fails()) {
            return response()->json([
                'mensaje' => 'Error en la validación del estudio',
                'errors' => $validator->errors(),
                'status' => 400
            ], 400);
        }

        // Buscar hoja de vida por documento
        $documento = $request->numDocumento;
        $hoja = Hojasvida::where('usuarioNumDocumento', $documento)->first();

        if (!$hoja) {
            return response()->json([
                'mensaje' => "Hoja de vida no encontrada para el documento $documento",
                'status' => 404
            ], 404);
        }

        // Buscar nombre del estudio
        $estudio = Estudios::find($request->idEstudios);
        if (!$estudio) {
            return response()->json([
                'mensaje' => 'Estudio no encontrado',
                'status' => 404
            ], 404);
        }

        $data = [
            'idHojaDeVida' => $hoja->idHojaDeVida,
            'idEstudios' => $request->idEstudios,
            'estado' => $request->estado
        ];

        // Guardar archivo si se adjunta
        if ($request->hasFile('archivo')) {
            $file = $request->file('archivo');
            $extension = $file->getClientOriginalExtension();

            // Limpiar el nombre del estudio para que no tenga espacios ni caracteres especiales
            $nombreCarrera = preg_replace('/[^A-Za-z0-9_\-]/', '_', $estudio->nomEstudio);
            $filename = $nombreCarrera . '_' . time() . '.' . $extension;
            $folder = 'Archivos/' . $documento;

            $path = $file->storeAs($folder, $filename, 'public');
            $data['archivo'] = 'storage/' . $path;
        }

        try {
            $registro = Hojasvidahasestudios::create($data);

            return response()->json([
                'mensaje' => 'Estudio agregado correctamente',
                'estudio' => $registro,
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
        $registro = Hojasvidahasestudios::with('estudio')->find($id);

        if (!$registro) {
            return response()->json([
                "mensaje" => "No se encontró el estudio con ID $id",
                "status" => 404
            ], 404);
        }

        return response()->json([
            "estudio" => $registro,
            "status" => 200
        ], 200);
    }

    public function update(Request $request, $id)
    {
        $registro = Hojasvidahasestudios::find($id);

        if (!$registro) {
            return response()->json([
                "mensaje" => "No se encontró el estudio",
                "status" => 404
            ], 404);
        }

        $validator = Validator::make($request->request->all(), [
            "idHojaDeVida" => "required|exists:hojasvida,idHojaDeVida",
            "idEstudios" => "required|exists:estudios,idEstudios",
            "estado" => "required|boolean",
        ]);

        if ($validator->fails()) {
            return response()->json([
                "mensaje" => "Error de validación",
                "errors" => $validator->errors(),
                "status" => 400
            ], 400);
        }

        try {
            $registro->idHojaDeVida = $request->input('idHojaDeVida');
            $registro->idEstudios = $request->input('idEstudios');
            $registro->estado = $request->input('estado');

            if ($request->hasFile("archivo")) {
                // 🔍 Buscar datos para generar ruta personalizada
                $hoja = Hojasvida::find($request->input('idHojaDeVida'));
                $estudio = Estudios::find($request->input('idEstudios'));

                if ($hoja && $estudio) {
                    // 🧹 Borrar archivo anterior si existe
                    if ($registro->archivo && Storage::disk("public")->exists($registro->archivo)) {
                        Storage::disk("public")->delete($registro->archivo);
                    }

                    // 📝 Nombre amigable
                    $documento = $hoja->usuarioNumDocumento;
                    $nombreCarrera = preg_replace('/[^A-Za-z0-9_\-]/', '_', $estudio->nomEstudio);
                    $extension = $request->file('archivo')->getClientOriginalExtension();
                    $filename = $nombreCarrera . '_' . time() . '.' . $extension;
                    $folder = 'Archivos/' . $documento;

                    // 📦 Guardar en storage/app/public/Archivos/{documento}
                    $path = $request->file('archivo')->storeAs($folder, $filename, 'public');

                    // 📌 Guardar ruta final
                    $registro->archivo = 'storage/' . $path;
                }
            }

            $registro->save();

            return response()->json([
                "mensaje" => "Estudio actualizado correctamente",
                "estudio" => $registro,
                "status" => 200
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                "mensaje" => "Error al actualizar el estudio",
                "error" => $e->getMessage(),
                "status" => 500
            ], 500);
        }
    }






    public function destroy($id)
    {
        $relacion = Hojasvidahasestudios::find($id);
        if (!$relacion) {
            return response()->json(['mensaje' => 'Relación no encontrada'], 404);
        }

        $relacion->delete();
        return response()->json(['mensaje' => 'Relación eliminada correctamente'], 200);
    }
    public function buscarPorDocumento($numDocumento)
    {
        // Buscar la hoja de vida por número de documento
        $hoja = Hojasvida::with('usuario')->where('usuarioNumDocumento', $numDocumento)->first();

        if (!$hoja) {
            return response()->json([
                "mensaje" => "Hoja de vida no encontrada",
                "status" => 404
            ], 404);
        }


        $estudios = Hojasvidahasestudios::with('estudio')
            ->where('idHojaDevida', $hoja->idHojaDeVida)
            ->get();

        return response()->json([
            "data" => [
                "hojaDeVida" => $hoja,
                "usuario" => $hoja->usuario,
                "estudios" => $estudios
            ],
            "status" => 200
        ]);
    }


    // Obtener todos los estudios por ID de hoja de vida
    public function buscarPorHojaDeVida($idHojaDeVida)
    {
        $registros = Hojasvidahasestudios::with('estudio')
            ->where('idHojaDevida', $idHojaDeVida)
            ->get();

        if ($registros->isEmpty()) {
            return response()->json([
                "mensaje" => "No se encontraron estudios para esta hoja de vida",
                "status" => 404
            ], 404);
        }

        return response()->json([
            "estudios" => $registros,
            "status" => 200
        ], 200);
    }
}
