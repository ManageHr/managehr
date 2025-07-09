<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Postulaciones;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

/**
 * @OA\Tag(
 *     name="Mis Postulaciones",
 *     description="Gestión de postulaciones del usuario autenticado. Permite a los usuarios externos consultar sus propias postulaciones a vacantes."
 * )
 */
class MisPostulacionesController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/mis-postulaciones",
     *     summary="Obtener mis postulaciones",
     *     description="Obtiene todas las postulaciones del usuario autenticado a diferentes vacantes",
     *     tags={"Mis Postulaciones"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Lista de postulaciones obtenida exitosamente",
     *         @OA\JsonContent(
     *             @OA\Property(property="data", type="array", 
     *                 @OA\Items(
     *                     type="object",
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="numdocumento", type="string", example="12345678"),
     *                     @OA\Property(property="vacante_id", type="integer", example=5),
     *                     @OA\Property(property="estado", type="string", example="Pendiente"),
     *                     @OA\Property(property="created_at", type="string", format="date-time"),
     *                     @OA\Property(property="updated_at", type="string", format="date-time"),
     *                     @OA\Property(property="vacante", type="object",
     *                         @OA\Property(property="id", type="integer", example=5),
     *                         @OA\Property(property="titulo", type="string", example="Desarrollador Full Stack"),
     *                         @OA\Property(property="descripcion", type="string", example="Descripción de la vacante"),
     *                         @OA\Property(property="salario", type="number", example=3000000),
     *                         @OA\Property(property="estado", type="string", example="Activa")
     *                     )
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Usuario no autenticado o sin número de documento",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Usuario no autenticado o sin número de documento.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Error interno del servidor",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Ocurrió un error al obtener tus postulaciones."),
     *             @OA\Property(property="error", type="string", example="Error detallado del servidor")
     *         )
     *     )
     * )
     */
    public function index()
    {
        try {
            $usuario = Auth::user();

            if (!$usuario || !isset($usuario->numdocumento)) {
                return response()->json(['message' => 'Usuario no autenticado o sin número de documento.'], 401);
            }

            // Carga la relación con la vacante
            $postulaciones = Postulaciones::with('vacante')
                ->where('numdocumento', $usuario->numdocumento)
                ->get();

            return response()->json([
                'data' => $postulaciones
            ], 200);

        } catch (\Exception $e) {
            Log::error('Error al obtener postulaciones del usuario (MisPostulacionesController@index): ' . $e->getMessage());

            return response()->json([
                'message' => 'Ocurrió un error al obtener tus postulaciones.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
