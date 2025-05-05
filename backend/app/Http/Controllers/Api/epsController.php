<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Eps;
use Illuminate\Support\Facades\Validator;

class epsController extends Controller
{
    public function index()
    {
    

        $epss = Eps::orderBy('nombreEps', 'asc')->get();;

        $data = [
            "eps" => $epss,
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
        $eps = Eps::where('codigoEps', $id)->first();
        if (!$eps) {
            $data = [
                "mensage" => " No se encontro eps",
                "status" => 201
            ];
            return response()->json([$data], 201);
        }
        $data = [
            "eps" => $eps,
            "status" => 200
        ];
        return response()->json([$data], 200);
    }
    public function destroy($id)
    {
        $data = [
            "mesaje " => "este modulo no permite eliminar, solo el administrador de base de datos lo puede hacer",
            "status" => 400
        ];
        return response()->json([$data], 400);
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
