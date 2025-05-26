<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Vacaciones;
use App\Models\Contrato;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class formvacationController extends Controller
{
    /**
     * Store a newly created vacation request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        Log::info('Solicitud recibida:', $request->all());

        // Validación (sin 'dias')
        $validator = Validator::make($request->all(), [
            'motivo'       => 'required|string|max:500',
            'fechaInicio'  => 'required|date_format:Y-m-d',
            'fechaFinal'   => 'required|date_format:Y-m-d|after_or_equal:fechaInicio',
            'contratoId'   => 'required|integer|exists:contrato,idContrato',
        ]);

        if ($validator->fails()) {
            Log::error('Error de validación:', ['errors' => $validator->errors()->toArray()]);
            return response()->json([
                'message' => 'Error de validación en los datos de la solicitud.',
                'errors'  => $validator->errors(),
            ], 422);
        }

        try {
            // Verificar autenticación
            if (! $usuario = Auth::user()) {
                return response()->json(['message' => 'Usuario no autenticado.'], 401);
            }

            // Verificar existencia del contrato
            $contrato = Contrato::find($request->input('contratoId'));
            if (! $contrato) {
                return response()->json(['message' => 'Contrato inválido o no encontrado.'], 404);
            }

            // Calcular automáticamente 'dias' (incluye ambos extremos)
            $inicio = Carbon::parse($request->input('fechaInicio'));
            $fin    = Carbon::parse($request->input('fechaFinal'));
            $dias   = $inicio->diffInDays($fin) + 1;

            // Crear la solicitud de vacaciones
            $vacacion = Vacaciones::create([
                'motivo'      => $request->input('motivo'),
                'fechaInicio'=> $request->input('fechaInicio'),
                'fechaFinal' => $request->input('fechaFinal'),
                'dias'        => $dias,
                'estado'      => 'pendiente',
                'contratoId'  => $contrato->idContrato,
            ]);

            Log::info('Solicitud de vacaciones guardada con éxito', [
                'idVacaciones' => $vacacion->idVacaciones
            ]);

            return response()->json([
                'message' => 'Solicitud de vacaciones guardada con éxito.',
                'data'    => $vacacion
            ], 201);

        } catch (\Exception $e) {
            Log::error('Error al guardar en la BD', ['error' => $e->getMessage()]);
            return response()->json([
                'message' => 'Error al guardar la solicitud de vacaciones en la base de datos.',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }
}
