<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Area;
use App\Models\Contrato;
use App\Models\Usuarios;
use OpenApi\Annotations as OA;

class jefePersonalController extends Controller

 {
    /**
     * @OA\Get(
     *     path="/api/jefes/{jefeId}/empleados",
     *     summary="Listar empleados por jefe de personal",
     *     description="Obtiene una lista de empleados que pertenecen al área asignada al jefe de personal indicado.",
     *     tags={"Jefes"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="jefeId",
     *         in="path",
     *         required=true,
     *         description="ID del jefe de personal",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Lista de empleados obtenida correctamente",
     *         @OA\JsonContent(
     *             @OA\Property(property="empleados", type="array", @OA\Items(type="object")),
     *             @OA\Property(property="area", type="string", example="Recursos Humanos"),
     *             @OA\Property(property="message", type="string", example="Empleados obtenidos correctamente")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="No se encontró área asignada para este jefe de personal",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="No se encontró área asignada para este jefe de personal"),
     *             @OA\Property(property="empleados", type="array", @OA\Items(type="object"))
     *         )
     *     )
     * )
     */

    public function empleadosPorJefe($jefeId)
    {
        
        $area = \DB::table('area')->where('idJefe', $jefeId)->first();
        if (!$area) {
            return response()->json(['message' => 'No se encontró área asignada para este jefe de personal', 'empleados' => []], 404);
        }
       
        $contratos = \DB::table('contrato')
            ->where('area', $area->idArea)
            ->where('cargoArea', 1)
            ->where('estado', 1)
            ->get();
      
        $empleados = [];
        foreach ($contratos as $contrato) {
            $hoja = \DB::table('hojasvida')->where('idHojaDeVida', $contrato->hojaDeVida)->first();
            if ($hoja) {
                $usuario = \DB::table('usuarios')->where('numDocumento', $hoja->usuarioNumDocumento)->first();
                if ($usuario) {
                    $user = \DB::table('users')->where('id', $usuario->usersId)->first();
                    if ($user) {
                        $user->perfil = $usuario;
                        $empleados[] = $user;
                    }
                }
            }
        }
       

        return response()->json([
            'empleados' => $empleados,
            'area' => $area->nombreArea,
            'message' => 'Empleados obtenidos correctamente'
        ]);
    }

    public function index()
    {
        
    }


    public function store(Request $request)
    {
        
    }

   
    public function show(string $id)
    {
        
    }

   
    public function update(Request $request, string $id)
    {
        
    }

  
    public function destroy(string $id)
    {
        
    }
    
    
}
