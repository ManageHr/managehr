<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Area;
use App\Models\Contrato;
use App\Models\Usuarios;

class jefePersonalController extends Controller
{

    public function empleadosPorJefe($jefeId)
    {
        // 1. Buscar el área del jefe
        $area = \DB::table('area')->where('idJefe', $jefeId)->first();
        if (!$area) {
            return response()->json(['message' => 'No se encontró área asignada para este jefe de personal', 'empleados' => []], 404);
        }
        // Depura el área
        // dd($area);

        // 2. Buscar contratos de empleados en esa área
        $contratos = \DB::table('contrato')
            ->where('area', $area->idArea)
            ->where('cargoArea', 1)
            ->where('estado', 1)
            ->get();
        // Depura los contratos
        // dd($contratos);

        // 3. Traer los usuarios de esos contratos
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
        // Depura los empleados
        // dd($empleados);

        return response()->json([
            'empleados' => $empleados,
            'area' => $area->nombreArea,
            'message' => 'Empleados obtenidos correctamente'
        ]);
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
    
    
}
