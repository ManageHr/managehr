<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Incapacidad;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class formincapacidadController extends Controller
{
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'descrip' => 'required|string',
            'archivo' => 'nullable|file|max:5000|mimes:pdf,doc,docx,jpg,png,jpeg',
            'fechaInicio' => 'required|date_format:Y-m-d',
            'fechaFinal' => 'required|date_format:Y-m-d|after_or_equal:fechaInicio',
            'contratoId' => 'required|integer|exists:contrato,idContrato',
        ]);

        if ($validator->fails()) {
            Log::error('Error de validación al guardar solicitud de incapacidad', [
                'errors' => $validator->errors()->toArray(),
                'request' => $request->all()
            ]);
            return response()->json([
                'message' => 'Error de validación en los datos de la solicitud de incapacidad.',
                'errors' => $validator->errors(),
            ], 422);
        }

        $filePath = '';

        if ($request->hasFile('archivo')) {
            $file = $request->file('archivo');

            try {
                $path = $file->store('uploads/incapacidades', 'public');
                $filePath = $path;
                Log::info('Archivo de incapacidad guardado', [
                    'path' => $filePath,
                    'original_name' => $file->getClientOriginalName()
                ]);
            } catch (\Exception $e) {
                Log::error('Error al guardar el archivo de incapacidad', [
                    'error' => $e->getMessage(),
                    'request_file' => $request->file('archivo') ? $request->file('archivo')->getClientOriginalName() : 'N/A',
                    'request_data' => $request->except('archivo')
                ]);
                return response()->json([
                    'message' => 'Error al guardar el archivo adjunto para la incapacidad.',
                    'error' => $e->getMessage(),
                ], 500);
            }
        } else {
            Log::info('No se adjuntó archivo a la solicitud de incapacidad.', [
                'request_data' => $request->all()
            ]);
        }

        try {
            $incapacidad = Incapacidad::create([
                'descrip' => $request->input('descrip'),
                'archivo' => $filePath,
                'fechaInicio' => $request->input('fechaInicio'),
                'fechaFinal' => $request->input('fechaFinal'),
                'contratoId' => $request->input('contratoId'),
            ]);

            Log::info('Solicitud de incapacidad guardada en DB', [
                'id' => $incapacidad->idIncapacidad,
                'contratoId' => $incapacidad->contratoId
            ]);

        } catch (ModelNotFoundException $e) {
            Log::error('Error de Modelo al guardar solicitud de incapacidad', [
                'error' => $e->getMessage(),
                'request' => $request->all()
            ]);
            return response()->json([
                'message' => 'Error de base de datos: El contrato especificado para la incapacidad no existe.',
                'error' => $e->getMessage(),
            ], 404);

        } catch (\Exception $e) {
            if (!empty($filePath) && Storage::disk('public')->exists($filePath)) {
                Storage::disk('public')->delete($filePath);
                Log::warning('Archivo adjunto eliminado tras fallo en inserción DB para incapacidad', [
                    'path' => $filePath
                ]);
            }

            Log::error('Error al guardar solicitud de incapacidad en DB', [
                'error' => $e->getMessage(),
                'request' => $request->all()
            ]);
            return response()->json([
                'message' => 'Error al guardar la solicitud de incapacidad en la base de datos.',
                'error' => $e->getMessage(),
            ], 500);
        }

        return response()->json([
            'message' => 'Solicitud de incapacidad guardada con éxito.',
            'data' => $incapacidad
        ], 201);
    }
}
