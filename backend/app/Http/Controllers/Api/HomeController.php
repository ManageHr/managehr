<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{

    /**
 * @OA\Get(
 *     path="/api/perfil",
 *     summary="Obtener el perfil del usuario autenticado",
 *     description="Retorna los datos del perfil con tipo de documento y género.",
 *     tags={"Perfil"},
 *     security={{"bearerAuth":{}}},
 *     @OA\Response(
 *         response=200,
 *         description="Perfil encontrado con éxito",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="id", type="integer", example=1),
 *             @OA\Property(property="nombre", type="string", example="Sharón"),
 *             @OA\Property(property="apellido", type="string", example="López"),
 *             @OA\Property(property="email", type="string", example="sharon@example.com"),
 *             @OA\Property(property="tipoDocumento", type="object",
 *                 @OA\Property(property="nombre", type="string", example="Cédula de Ciudadanía")
 *             ),
 *             @OA\Property(property="genero", type="object",
 *                 @OA\Property(property="nombre", type="string", example="Femenino")
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=401,
 *         description="Usuario no autenticado"
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Perfil no encontrado"
 *     )
 * )
 */

    
    
    public function getProfile()
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json(['message' => 'Usuario no autenticado'], 401);
        }

       
        $perfil = $user->perfil()->with('tipoDocumento', 'genero')->first();

        if (!$perfil) {
            return response()->json(['message' => 'Perfil no encontrado'], 404);
        }

        return response()->json($perfil);
    }


    /**
     * @OA\Put(
     *     path="/api/perfil",
     *     summary="Actualizar el perfil del usuario autenticado",
     *     description="Permite actualizar información básica del perfil del usuario.",
     *     tags={"Perfil"},
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="email", type="string", format="email", example="usuario@example.com"),
     *             @OA\Property(property="direccion", type="string", example="Calle 123 #45-67"),
     *             @OA\Property(property="telefono", type="string", example="3001234567"),
     *             @OA\Property(property="numHijos", type="integer", example=2),
     *             @OA\Property(property="contactoEmergencia", type="string", example="Juan Pérez"),
     *             @OA\Property(property="numContactoEmergencia", type="string", example="3123456789"),
     *             @OA\Property(property="estadoCivilId", type="integer", example=1)
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Perfil actualizado con éxito"
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Usuario no autenticado"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Perfil no encontrado"
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Datos inválidos enviados"
     *     )
     * )
     */

    public function updateProfile(Request $request)
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json(['message' => 'Usuario no autenticado'], 401);
        }

        $perfil = $user->perfil;

        if (!$perfil) {
            return response()->json(['message' => 'Perfil no encontrado'], 404);
        }

        $validated = $request->validate([
            'email' => 'required|email|max:255',
            'direccion' => 'nullable|string|max:255',
            'telefono' => 'nullable|string|max:20',
            'numHijos' => 'nullable|integer',
            'contactoEmergencia' => 'nullable|string|max:255',
            'numContactoEmergencia' => 'nullable|string|max:20',
            'estadoCivilId' => 'nullable|integer'
        ]);

        $perfil->update($validated);

        return response()->json([
            'message' => 'Perfil actualizado con éxito',
            'user' => $perfil
        ]);
    }
}
