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
/**
 * @OA\Tag(
 *     name="Vacaciones Empleado",
 *     description="Solicitudes de vacaciones realizadas por empleados internos. Permite registrar y consultar solicitudes propias de vacaciones."
 * )
 */

class formvacationController extends Controller
{
    /**
     * Display a listing of the authenticated user's vacation requests.
     *
     * @return \Illuminate\Http\JsonResponse
     */
        /**
     * @OA\Get(
     *     path="/api/solicitudes-vacaciones-con-archivo",
     *     summary="Obtener solicitudes de vacaciones del usuario autenticado",
     *     tags={"Vacaciones Empleado"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Lista de solicitudes de vacaciones",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(type="object")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Usuario no autenticado"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="No se encontró contrato para el usuario"
     *     )
     * )
     */

    public function index()
    {
        if (! $usuario = Auth::user()) {
            return response()->json(['message' => 'Usuario no autenticado.'], 401);
        }

        $numDocumento = $usuario->perfil->usuarioNumDocumento
                      ?? $usuario->perfil->numDocumento
                      ?? $usuario->numdocumento
                      ?? null;

        if (! $numDocumento) {
            return response()->json(['message' => 'No se pudo identificar al usuario.'], 404);
        }

        $contrato = Contrato::whereHas('hojaDeVida', function ($q) use ($numDocumento) {
            $q->where('usuarioNumDocumento', $numDocumento);
        })->first();

        if (! $contrato) {
            return response()->json(['message' => 'No se encontró contrato para el usuario.'], 404);
        }

        $solicitudes = Vacaciones::where('contratoId', $contrato->idContrato)
                                 ->orderBy('idVacaciones', 'desc')
                                 ->get();

        return response()->json($solicitudes, 200);
    }

        /**
     * @OA\Post(
     *     path="/api/solicitudes-vacaciones-con-archivo",
     *     summary="Registrar una nueva solicitud de vacaciones",
     *     tags={"Vacaciones Empleado"},
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"motivo", "fechaInicio", "fechaFinal", "contratoId"},
     *             @OA\Property(property="motivo", type="string", example="Motivo de la solicitud"),
     *             @OA\Property(property="fechaInicio", type="string", format="date", example="2025-07-01"),
     *             @OA\Property(property="fechaFinal", type="string", format="date", example="2025-07-10"),
     *             @OA\Property(property="contratoId", type="integer", example=15)
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Solicitud de vacaciones registrada correctamente",
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
     *         response=401,
     *         description="Usuario no autenticado"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Error interno al guardar la solicitud"
     *     )
     * )
     */

    public function store(Request $request)
    {
        Log::info('Solicitud recibida:', $request->all());

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
            if (! $usuario = Auth::user()) {
                return response()->json(['message' => 'Usuario no autenticado.'], 401);
            }

            // Asegúrate de que exista un contrato con ese ID
            $contrato = Contrato::find($request->input('contratoId'));
            if (! $contrato) {
                return response()->json(['message' => 'Contrato inválido o no encontrado.'], 404);
            }

            // Calcular días (inclusive)
            $inicio = Carbon::parse($request->input('fechaInicio'));
            $fin    = Carbon::parse($request->input('fechaFinal'));
            $dias   = $inicio->diffInDays($fin) + 1;

            // Crear instancia y asignar atributos MANUALMENTE
            $vacacion = new Vacaciones();
            $vacacion->motivo      = $request->input('motivo');
            $vacacion->fechaInicio = $request->input('fechaInicio');
            $vacacion->fechaFinal  = $request->input('fechaFinal');
            // Ojo: asegúrate de que tu columna en BD **se llame** exactamente 'contratoId'.
            // Si tu columna se llama distinto (p.ej. 'contratold'), cámbialo aquí:
            $vacacion->contratoId  = $contrato->idContrato;
            $vacacion->dias         = $dias;
            $vacacion->estado      = 'pendiente';

            $vacacion->save();

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
