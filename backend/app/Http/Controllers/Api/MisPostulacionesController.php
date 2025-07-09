<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Postulaciones;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;


class MisPostulacionesController extends Controller
{
    
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
