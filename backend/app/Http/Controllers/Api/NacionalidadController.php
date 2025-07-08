<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Nacionalidad;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;


class NacionalidadController extends Controller
{

     /**
     * @OA\Get(
     *     path="/api/nacionalidades",
     *     summary="Listar todas las nacionalidades",
     *     description="Devuelve un listado ordenado alfabéticamente de todas las nacionalidades registradas.",
     *     tags={"Nacionalidades"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Listado obtenido correctamente",
     *         @OA\JsonContent(
     *             @OA\Property(property="Nacionalidad", type="array", @OA\Items(type="object")),
     *             @OA\Property(property="status", type="integer", example=200)
     *         )
     *     )
     * )
     */

    public function index(){
        $nacionalidad = Nacionalidad::orderBy('nombre', 'asc')->get();

       
        $data=[
            "Nacionalidad" => $nacionalidad,
            "status" => 200
        ];
        return response()->json($data,200);
       


    }
    public function store(Request $request){
        $data=[
            "mesaje " => "este modulo no permite crear, solo el administrador de base de datos lo puede hacer",
            "status" => 400
        ];
        return response()->json([$data],400);
    



    }
    public function show($id){
        $Nacionalidad=Nacionalidad::find($id);
        if(!$Nacionalidad){
            $data=[
                "mensage" => " No se encontro Nacionalidad",
                "status" => 201
            ];
            return response()->json([$data],201);
        }
        $data=[
            "Nacionalidad" => $Nacionalidad,
            "status" => 200
        ];
        return response()->json([$data],200);


    }
    public function destroy($id){
        $data=[
            "mesaje " => "este modulo no permite eliminar, solo el administrador de base de datos lo puede hacer",
            "status" => 400
        ];
        return response()->json([$data],400);


    }
    public function update(Request $request,$id){
        $data=[
            "mesaje " => "este modulo no permite Actualizar, solo el administrador de base de datos lo puede hacer",
            "status" => 400
        ];
        return response()->json([$data],400);
        
    }



    public function updatePartial(Request $request,$id){
        $data=[
            "mesaje " => "este modulo no permite actualizar, solo el administrador de base de datos lo puede hacer",
            "status" => 400
        ];
        return response()->json([$data],400);
    }
}
