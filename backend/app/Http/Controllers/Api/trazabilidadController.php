<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Trazabilidad;
use Illuminate\Http\Request;

class trazabilidadController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $tipodocumento = Trazabilidad::all();

        $data = [
            "tipodocumento" => $tipodocumento,
            "status" => 200
        ];
        return response()->json($data, 200);
        //return "Obteniendo lista de epss del contepsador";

    }
    public function store(Request $request)
    {
        $data = [
            "mesaje " => "este modulo no permite crear, solo el administrador de base de datos lo puede hacer",
            "status" => 400
        ];
        return response()->json([$data], 400);
    }
    public function show($id)
    {
        $tipodocumento = Trazabilidad::find($id);
        if (!$tipodocumento) {
            $data = [
                "mensage" => " No se encontro el tipo de contrato",
                "status" => 201
            ];
            return response()->json([$data], 201);
        }
        $data = [
            "tipodocumento" => $tipodocumento,
            "status" => 200
        ];
        return response()->json([$data], 200);
    }
    public function destroy($id)
    {
        $trazabilidad = Trazabilidad::find($id);
        if (!$trazabilidad) {
            $data = [
                "mensage" => " No se encontro trazabilidad",
                "status" => 404
            ];
            return response()->json([$data], 404);
        } else {
            $trazabilidad->delete();
            $data = [
                "permisos" => 'trazabilidad eliminada',
                "status" => 200
            ];
            return response()->json([$data], 200);
        }
    }
    public function update(Request $request, $id)
    {
        $data = [
            "mesaje " => "este modulo no permite Actualizar, solo el administrador de base de datos lo puede hacer",
            "status" => 400
        ];
        return response()->json([$data], 400);
    }
    public function updatePartial(Request $request, $id)
    {
        $data = [
            "mesaje " => "este modulo no permite actualizar, solo el administrador de base de datos lo puede hacer",
            "status" => 400
        ];
        return response()->json([$data], 400);
    }
}
