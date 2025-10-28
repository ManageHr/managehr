<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\HorasExtra;
use App\Models\Contrato;
use App\Models\HojasVida;
use Illuminate\Support\Facades\Auth;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

/**
 * @OA\Tag(
 *     name="Horas Extra Empleado",
 *     description="Solicitudes de horas extra realizadas por empleados internos. Permite registrar y consultar solicitudes propias de horas extra."
 * )
 */

class FormHorasController extends Controller
{
    /**
     * @OA\Post(
     *     path="/api/horas-extra",
     *     summary="Registrar una nueva solicitud de horas extra",
     *     description="Permite a un usuario autenticado registrar una solicitud de horas extra asociada a su contrato actual.",
     *     tags={"Horas Extra Empleado"},
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"fecha", "tipoHorasId", "nHorasExtra"},
     *             @OA\Property(property="descripcion", type="string", example="Trabajo fuera de horario"),
     *             @OA\Property(property="fecha", type="string", format="date", example="2025-07-08"),
     *             @OA\Property(property="tipoHorasId", type="integer", example=1),
     *             @OA\Property(property="nHorasExtra", type="integer", example=3)
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Solicitud enviada correctamente.",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Solicitud enviada correctamente."),
     *             @OA\Property(property="data", type="object")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Error de validación o falta de hoja de vida/contrato.",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="No se encontró la hoja de vida."),
     *             @OA\Property(property="errors", type="object", nullable=true)
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Error interno del servidor.",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Error al guardar la solicitud."),
     *             @OA\Property(property="error", type="string")
     *         )
     *     )
     * )
     */

    public function store(Request $request)
    {

        $usuario = Auth::auth()->user();
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

    /**
     * @OA\Get(
     *     path="/api/horas-extra",
     *     summary="Obtener solicitudes de horas extra del usuario autenticado",
     *     description="Retorna todas las solicitudes de horas extra asociadas al contrato del usuario autenticado.",
     *     tags={"Horas Extra Empleado"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Listado de horas extra",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(type="object")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Error al obtener las horas extra",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Error al obtener las horas extra"),
     *             @OA\Property(property="error", type="string")
     *         )
     *     )
     * )
     */
    public function index(Request $request)
    {
        try {
            $usuario = Auth::auth()->user();
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
