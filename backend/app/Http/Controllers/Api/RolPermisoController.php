<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Rol;
use App\Models\Permisos;

class RolPermisoController extends Controller
{
    public function asignarPermisos(Request $request, $rol_id)
    {
        $rol = Rol::findOrFail($rol_id);

        // Validar que se envíe un array de IDs de permisos
        $request->validate([
            'permisos' => 'required|array',
            'permisos.*' => 'exists:permisos,id',
        ]);

        // Sincronizar permisos (elimina los anteriores y agrega los nuevos)
        $rol->permisos()->sync($request->permisos);

        return response()->json([
            'message' => 'Permisos asignados correctamente 🎉',
        ]);
    }
}
