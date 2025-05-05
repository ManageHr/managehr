<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\TipoContrato;
use Illuminate\Http\Request;

class tipoContratoController extends Controller
{
    public function index()
    {
        $tipocontrato = TipoContrato::all();

        $data = [
            "tipocontrato" => $tipocontrato,
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
        $tipocontrato = TipoContrato::where('idTipoContrato', $id)->first();
        if (!$tipocontrato) {
            $data = [
                "mensage" => " No se encontro el tipo de contrato",
                "status" => 201
            ];
            return response()->json([$data], 201);
        }
        $data = [
            "tipocontrato" => $tipocontrato,
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
