<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Tipohoras;
use Illuminate\Support\Facades\Validator;

class tipohorasController extends Controller
{
    public function index()
    {
        $tipohoras = Tipohoras::all();

        $data = [
            "tipohoras" => $tipohoras,
            "status" => 200
        ];
        return response()->json($data, 200);
        //return "Obteniendo lista de epss del contepsador";

    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = [
            "mesaje " => "este modulo no permite crear, solo el administrador de base de datos lo puede hacer",
            "status" => 400
        ];
        return response()->json([$data], 400);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $tipohoras = Tipohoras::find($id);
        if (!$tipohoras) {
            $data = [
                "mensage" => " No se encontro tipoHora",
                "status" => 201
            ];
            return response()->json([$data], 201);
        }
        $data = [
            "Tipohoras" => $tipohoras,
            "status" => 200
        ];
        return response()->json([$data], 200);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
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

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $data = [
            "mesaje " => "este modulo no permite Actualizar, solo el administrador de base de datos lo puede hacer",
            "status" => 400
        ];
        return response()->json([$data], 400);
    }
}
