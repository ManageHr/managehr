<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Postulaciones;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use OpenApi\Annotations as OA;

class MisPostulacionesController extends Controller
{

     /**
     * @OA\Get(
     *     path="/api/mis-postulaciones",
     *     summary="Obtener postulaciones del usuario autenticado",
     *     description="Devuelve las postulaciones realizadas por el usuario autenticado, incluyendo los datos de la vacante.",
     *     tags={"Postulaciones"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Postulaciones obtenidas correctamente",
     *         @OA\JsonContent(
     *             @OA\Property(property="data", type="array", @OA\Items(type="object"))
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
     *         description="Error interno al obtener las postulaciones",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Ocurrió un error al obtener tus postulaciones."),
     *             @OA\Property(property="error", type="string", example="Detalle del error")
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
