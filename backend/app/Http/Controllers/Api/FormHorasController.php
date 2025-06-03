<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\HorasExtra;
use App\Models\Contrato;
use App\Models\HojasVida;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class FormHorasController extends Controller
{
    public function store(Request $request)
    {
        // Aquí ya no esperas contratoId desde el frontend
        $usuario = auth()->user();
        $documento = optional($usuario->perfil)['numDocumento'];

        if (!$documento) {
            return response()->json(['message' => 'No se pudo obtener el documento del usuario.'], 422);
        }

        $hoja = HojasVida::where('usuarioNumDocumento', $documento)->first();
        if (!$hoja) {
            return response()->json(['message' => 'No se encontró la hoja de vida.'], 422);
        }

        $contrato = Contrato::where('hojaDeVida', $hoja->idHojaDeVida)->first();
        if (!$contrato) {
            return response()->json(['message' => 'No se encontró contrato asociado.'], 422);
        }

        $validator = Validator::make($request->all(), [
            'descripcion'   => 'nullable|string',
            'fecha'         => 'required|date',
            'tipoHorasId'   => 'required|integer',
            'nHorasExtra'   => 'required|integer|min:1',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Datos inválidos.',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $hora = HorasExtra::create([
                'descripcion'   => $request->input('descripcion'),
                'fecha'         => $request->input('fecha'),
                'tipoHorasId'   => $request->input('tipoHorasId'),
                'nHorasExtra'   => $request->input('nHorasExtra'),
                'contratoId'    => $contrato->idContrato,
            ]);

            return response()->json([
                'message' => 'Solicitud enviada correctamente.',
                'data' => $hora
            ], 201);

        } catch (\Exception $e) {
            Log::error('Error al guardar horas extra', ['error' => $e->getMessage()]);
            return response()->json([
                'message' => 'Error al guardar la solicitud.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function index(Request $request)
    {
        try {
            $usuario = auth()->user();
            $documento = optional($usuario->perfil)['numDocumento'];

            if (!$documento) {
                return response()->json([], 200);
            }

            $hoja = HojasVida::where('usuarioNumDocumento', $documento)->first();
            if (!$hoja) {
                return response()->json([], 200);
            }

            $contrato = Contrato::where('hojaDeVida', $hoja->idHojaDeVida)->first();
            if (!$contrato) {
                return response()->json([], 200);
            }

            $horas = HorasExtra::where('contratoId', $contrato->idContrato)
                ->orderByDesc('fecha')
                ->get();

            return response()->json($horas, 200);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al obtener las horas extra',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
