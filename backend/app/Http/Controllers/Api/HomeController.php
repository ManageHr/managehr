<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    /**
     * Obtener el perfil del usuario autenticado desde la tabla 'usuarios'
     * con las relaciones tipoDocumento y genero.
     */
    public function getProfile()
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json(['message' => 'Usuario no autenticado'], 401);
        }

        // Cargar perfil con relaciones
        $perfil = $user->perfil()->with('tipoDocumento', 'genero')->first();

        if (!$perfil) {
            return response()->json(['message' => 'Perfil no encontrado'], 404);
        }

        return response()->json($perfil);
    }

    /**
     * Actualizar el perfil del usuario en la tabla 'usuarios'
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

        // Validar únicamente los campos que realmente se pueden actualizar desde el frontend
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
