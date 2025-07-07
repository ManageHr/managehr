<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Nacionalidad;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use OpenApi\Annotations as OA;


class NacionalidadController extends Controller
{

    /**
     * @OA\Get(
     *     path="/api/nacionalidad",
     *     summary="Listar todas las nacionalidades",
     *     description="Obtiene una lista de todas las nacionalidades ordenadas alfabéticamente.",
     *     tags={"Nacionalidad"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Listado obtenido exitosamente",
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

        /**
     * @OA\Post(
     *     path="/api/nacionalidad",
     *     summary="Crear nacionalidad (no permitido)",
     *     description="Este módulo no permite crear nacionalidades, solo el administrador de base de datos puede hacerlo.",
     *     tags={"Nacionalidad"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=400,
     *         description="Operación no permitida"
     *     )
     * )
     */


    public function store(Request $request){
        $data=[
            "mesaje " => "este modulo no permite crear, solo el administrador de base de datos lo puede hacer",
            "status" => 400
        ];
        return response()->json([$data],400);
        

    /**
     * @OA\Get(
     *     path="/api/nacionalidad/{id}",
     *     summary="Obtener una nacionalidad por ID",
     *     description="Devuelve la nacionalidad correspondiente al ID proporcionado.",
     *     tags={"Nacionalidad"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID de la nacionalidad",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Nacionalidad encontrada",
     *         @OA\JsonContent(
     *             @OA\Property(property="Nacionalidad", type="object"),
     *             @OA\Property(property="status", type="integer", example=200)
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Nacionalidad no encontrada"
     *     )
     * )
     */
    
     
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

    /**
     * @OA\Delete(
     *     path="/api/nacionalidad/{id}",
     *     summary="Eliminar nacionalidad (no permitido)",
     *     description="Este módulo no permite eliminar nacionalidades, solo el administrador de base de datos puede hacerlo.",
     *     tags={"Nacionalidad"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID de la nacionalidad a eliminar",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Operación no permitida"
     *     )
     * )
     */


    public function destroy($id){
        $data=[
            "mesaje " => "este modulo no permite eliminar, solo el administrador de base de datos lo puede hacer",
            "status" => 400
        ];
        return response()->json([$data],400);
    }


     /**
     * @OA\Put(
     *     path="/api/nacionalidad/{id}",
     *     summary="Actualizar nacionalidad (no permitido)",
     *     description="Este módulo no permite actualizar nacionalidades, solo el administrador de base de datos puede hacerlo.",
     *     tags={"Nacionalidad"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID de la nacionalidad a actualizar",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Operación no permitida"
     *     )
     * )
     */


    public function update(Request $request,$id){
        $data=[
            "mesaje " => "este modulo no permite Actualizar, solo el administrador de base de datos lo puede hacer",
            "status" => 400
        ];
        return response()->json([$data],400);
        
    }

    /**
     * @OA\Patch(
     *     path="/api/nacionalidad/{id}",
     *     summary="Actualizar nacionalidad parcialmente (no permitido)",
     *     description="Este módulo no permite actualización parcial, solo el administrador de base de datos puede hacerlo.",
     *     tags={"Nacionalidad"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID de la nacionalidad",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Operación no permitida"
     *     )
     * )
     */

     
    public function updatePartial(Request $request,$id){
        $data=[
            "mesaje " => "este modulo no permite actualizar, solo el administrador de base de datos lo puede hacer",
            "status" => 400
        ];
        return response()->json([$data],400);
    }
}
