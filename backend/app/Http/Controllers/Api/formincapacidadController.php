<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Incapacidad;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Auth;

/**
 * @OA\Tag(
 *     name="Incapacidades",
 *     description="Gestión de solicitudes de incapacidades"
 * )
 */

class formincapacidadController extends Controller
{
    /**
     * @OA\Post(
     *     path="/api/solicitudes-incapacidades",
     *     summary="Registrar una nueva solicitud de incapacidad",
     *     tags={"Incapacidades"},
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 required={"fechaInicio", "fechaFinal", "contratoId"},
     *                 @OA\Property(property="archivo", type="string", format="binary", description="Archivo adjunto"),
     *                 @OA\Property(property="fechaInicio", type="string", format="date", example="2025-07-08"),
     *                 @OA\Property(property="fechaFinal", type="string", format="date", example="2025-07-10"),
     *                 @OA\Property(property="contratoId", type="integer", example=12)
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Solicitud registrada exitosamente",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string"),
     *             @OA\Property(property="data", type="object")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Error de validación"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Error al guardar la solicitud"
     *     )
     * )
     */

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
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
                $path = $file->store('storage/Archivos/incapacidades', 'public');
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
    /**
     * @OA\Get(
     *     path="/api/solicitudes-incapacidades",
     *     summary="Obtener solicitudes de incapacidad del usuario autenticado",
     *     tags={"Incapacidades"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Listado de solicitudes",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(type="object")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Error al consultar las solicitudes"
     *     )
     * )
     */

    public function index(Request $request)
    {
        try {
            $usuario = Auth::auth()->user();
            Log::info('Usuario autenticado', ['usuario' => $usuario]);

            $documento = $usuario->perfil['numDocumento'] ?? null;

            if (!$documento) {
                Log::warning('El usuario no tiene numDocumento en el perfil');
                return response()->json([], 200);
            }

            $hoja = \App\Models\HojasVida::where('usuarioNumDocumento', $documento)->first();
            Log::info('Hoja de vida encontrada', ['hoja' => $hoja]);

            if (!$hoja) {
                Log::warning('No se encontró hoja de vida');
                return response()->json([], 200);
            }

            // 🔽 Este log te dirá con exactitud qué valor se está buscando
            Log::debug('Buscando contrato con hojaDeVida = ' . $hoja->idHojaDeVida);

            $contrato = \App\Models\Contrato::where('hojaDeVida', $hoja->idHojaDeVida)->first();
            Log::info('Contrato encontrado', ['contrato' => $contrato]);

            if (!$contrato) {
                Log::warning('No se encontró contrato asociado a la hoja de vida');
                return response()->json([], 200);
            }

            $incapacidades = \App\Models\Incapacidad::where('contratoId', $contrato->idContrato)
                ->orderByDesc('fechaInicio')
                ->get();

            Log::info('Incapacidades encontradas', ['incapacidades' => $incapacidades]);

            return response()->json($incapacidades, 200);
        } catch (\Exception $e) {
            Log::error('Error al obtener solicitudes de incapacidad', ['error' => $e->getMessage()]);
            return response()->json([
                'message' => 'Error al obtener las solicitudes',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
