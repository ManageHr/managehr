<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Rol;
use App\Models\Permisos;

/**
 * @OA\Tag(
 *     name="Roles y Permisos",
 *     description="Gestión de asignación de permisos a roles"
 * )
 */

class RolPermisoController extends Controller
{

    /**
     * @OA\Post(
     *     path="/api/rol/{rol_id}/permisos",
     *     summary="Asignar permisos a un rol",
     *     description="Reemplaza los permisos actuales del rol con los proporcionados.",
     *     tags={"Roles y Permisos"},
     *     security={{"bearerAuth":{}}},
     *
     *     @OA\Parameter(
     *         name="rol_id",
     *         in="path",
     *         required=true,
     *         description="ID del rol al que se le asignarán los permisos",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *
     *     @OA\RequestBody(
     *         required=true,
     *         description="Lista de IDs de permisos",
     *         @OA\JsonContent(
     *             required={"permisos"},
     *             @OA\Property(
     *                 property="permisos",
     *                 type="array",
     *                 @OA\Items(type="integer", example=2)
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Permisos asignados correctamente",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Permisos asignados correctamente 🎉")
     *         )
     *     ),
     *     @OA\Response(response=422, description="Error de validación"),
     *     @OA\Response(response=404, description="Rol no encontrado")
     * )
     */
    
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
