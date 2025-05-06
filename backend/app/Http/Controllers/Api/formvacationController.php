<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Vacaciones;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

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

        //dd($request->all());
        
        Log::info('Solicitud recibida:', $request->all()); // Depuración: Verificar datos recibidos

        // Validación de datos
        $validator = Validator::make($request->all(), [
            'motivo' => 'required|string|max:500', // Se cambia 'descrip' por 'motivo' para que coincida con la BD
            'fechaInicio' => 'required|date_format:Y-m-d',
            'fechaFinal' => 'required|date_format:Y-m-d|after_or_equal:fechaInicio',
            'contratoId' => 'required|integer|exists:contrato,idContrato',
            'dias' => 'required|integer|min:1',
        ]);

        if ($validator->fails()) {
            Log::error('Error de validación:', ['errors' => $validator->errors()->toArray()]);
            return response()->json([
                'message' => 'Error de validación en los datos de la solicitud.',
                'errors' => $validator->errors(),
            ], 422);
        }

        // Guardar los datos en la base de datos
        try {
            $vacacion = Vacaciones::create([
                'motivo' => $request->input('motivo'),
                'fechaInicio' => $request->input('fechaInicio'),
                'fechaFinal' => $request->input('fechaFinal'),
                'contratoId' => $request->input('contratoId'),
                'dias' => $request->input('dias'),
                'estado' => 'pendiente', // Se asegura que el estado predeterminado es 'pendiente'
            ]);

            Log::info('Solicitud de vacaciones guardada con éxito', ['idVacaciones' => $vacacion->idVacaciones]);

            return response()->json([
                'message' => 'Solicitud de vacaciones guardada con éxito.',
                'data' => $vacacion
            ], 201);

        } catch (\Exception $e) {
            Log::error('Error al guardar en la BD', ['error' => $e->getMessage()]);
            return response()->json([
                'message' => 'Error al guardar la solicitud de vacaciones en la base de datos.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
