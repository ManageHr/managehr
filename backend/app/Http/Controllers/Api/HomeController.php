<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Usuarios;

class HomeController extends Controller
{
    /**
     * Obtener el perfil del usuario autenticado desde la tabla 'usuarios'
     */
    public function getProfile()
    {
        $user = Auth::user(); // Obtener el usuario autenticado

        if (!$user) {
            return response()->json(['message' => 'Usuario no autenticado'], 401);
        }

        $perfil = Usuarios::where('numDocumento', $user->numDocumento)->first();

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

        // Buscar el usuario en la tabla 'usuarios'
        $perfil = Usuarios::where('numDocumento', $user->numDocumento)->first();

        if (!$perfil) {
            return response()->json(['message' => 'Perfil no encontrado'], 404);
        }

        // Validaciones antes de actualizar
        $request->validate([
            'primerNombre' => 'required|string|max:255',
            'primerApellido' => 'required|string|max:255',
            'tipoDocumentoId' => 'required|integer',
            'numDocumento' => 'required|string|max:50',
            'fechaNac' => 'nullable|date',
            'email' => 'required|email|max:255|unique:usuarios,email,' . $perfil->numDocumento,
            'direccion' => 'nullable|string|max:255',
            'telefono' => 'nullable|string|max:20',
            'generoId' => 'nullable|integer',
            'numHijos' => 'nullable|integer',
            'contactoEmergencia' => 'nullable|string|max:255',
            'numContactoEmergencia' => 'nullable|string|max:20',
            'nacionalidadId' => 'nullable|integer',
            'epsCodigo' => 'nullable|integer',
            'estadoCivilId' => 'nullable|integer',
            'pensionesCodigo' => 'nullable|integer'
        ]);

        // Actualizar el perfil del usuario
        $perfil->update($request->all());

        return response()->json(['message' => 'Perfil actualizado con éxito', 'user' => $perfil]);
    }
}
