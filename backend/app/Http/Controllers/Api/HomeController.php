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
     *         description="Perfil encontrado con éxito"
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
