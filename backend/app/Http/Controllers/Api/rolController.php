<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Rol;
use Illuminate\Support\Facades\Validator;

/**
 * @OA\Tag(
 *     name="Roles",
 *     description="Gestión de roles de usuario"
 * )
 */

class rolController extends Controller
{

    /**
     * @OA\Get(
     *     path="/api/rol",
     *     summary="Listar todos los roles",
     *     tags={"Roles"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(response=200, description="Lista de roles")
     * )
     */

    public function index(){
        $rols=Rol::all();
        $data=[
            "rol" => $rols,
            "status" => 200
        ];
        return response()->json($data,200);
    }

    /**
     * @OA\Post(
     *     path="/api/rol",
     *     summary="Crear un nuevo rol",
     *     tags={"Roles"},
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"nombreRol"},
     *             @OA\Property(property="nombreRol", type="string", example="Administrador")
     *         )
     *     ),
     *     @OA\Response(response=201, description="Rol creado correctamente"),
     *     @OA\Response(response=400, description="Error de validación"),
     *     @OA\Response(response=500, description="Error interno")
     * )
     */


    public function store(Request $request){
        $validator=Validator::make($request->all(),[
            "nombreRol" => "required|min:3|max:30"
        ]);
        if($validator->fails()){
            $data=[
                "mesaje " => "Error en la validacion de rols",
                "errors" => $validator->errors(),
                "status" => 400
            ];
            return response()->json([$data],400);
        }
        try {
            $rol = Rol::create([
                "nombreRol" => $request->nombreRol
            ]);

            return response()->json([
                "mensaje" => "Rol creado correctamente",
                "rol" => $rol,
                "status" => 201
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                "mensaje" => "Error al crear el rol",
                "error" => $e->getMessage(),
                "status" => 500
            ], 500);

        }

    }


    /**
     * @OA\Get(
     *     path="/api/rol/{id}",
     *     summary="Mostrar un rol por ID",
     *     tags={"Roles"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID del rol",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(response=200, description="Rol encontrado"),
     *     @OA\Response(response=201, description="Rol no encontrado")
     * )
     */

    public function show($id){
        $rol=Rol::find($id);
        if(!$rol){
            $data=[
                "mensage" => " No se encontro rol",
                "status" => 201
            ];
            return response()->json([$data],201);
        }
        $data=[
            "rol" => $rol,
            "status" => 200
        ];
        return response()->json([$data],200);

    }

    /**
     * @OA\Delete(
     *     path="/api/rol/{id}",
     *     summary="Eliminar un rol",
     *     tags={"Roles"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID del rol a eliminar",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(response=200, description="Rol eliminado"),
     *     @OA\Response(response=404, description="Rol no encontrado")
     * )
     */


    public function destroy($id){
        $rol=Rol::find($id);
        if(!$rol){
            $data=[
                "mensage" => " No se encontro rol",
                "status" => 404
            ];
            return response()->json([$data],404);
        }
        $rol->delete();
        $data=[
            "rol" => 'rol eliminado',
            "status" => 200
        ];
        return response()->json([$data],200);
    }

    /**
     * @OA\Put(
     *     path="/api/rol/{id}",
     *     summary="Actualizar un rol",
     *     tags={"Roles"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID del rol a actualizar",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"nombreRol"},
     *             @OA\Property(property="nombreRol", type="string", example="Coordinador")
     *         )
     *     ),
     *     @OA\Response(response=200, description="Rol actualizado"),
     *     @OA\Response(response=400, description="Error de validación"),
     *     @OA\Response(response=404, description="Rol no encontrado"),
     *     @OA\Response(response=500, description="Error interno")
     * )
     */


    public function update(Request $request,$id){
        $rol=Rol::find($id);
        if(!$rol){
            $data=[
                "mensage" => " No se encontro rol",
                "status" => 404
            ];
            return response()->json([$data],404);
        }
        $validator = Validator::make($request->all(),[
            "nombreRol" => "required|min:3|max:30"
        ]);
        if($validator->fails()){
            $data=[
                "errors" => $validator->errors(),
                "status" => 400
            ];
            return response()->json([$data],400);
        }
        $rol->nombreRol=$request->nombreRol;
        try{
            $rol->save();
            $data=[
                "rol" => $rol,
                 "status" => 200
            ];
            return response()->json([$data],200);
        } catch (\Exception $e) {
            return response()->json([
                "mensaje" => "Error al modificar el rol",
                "error" => $e->getMessage(),
                "status" => 500
            ], 500);

        }

    }

    /**
     * @OA\Patch(
     *     path="/api/rol/{id}",
     *     summary="Actualizar parcialmente un rol",
     *     tags={"Roles"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID del rol a actualizar parcialmente",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         @OA\JsonContent(
     *             @OA\Property(property="nombreRol", type="string", example="Analista")
     *         )
     *     ),
     *     @OA\Response(response=200, description="Rol actualizado parcialmente"),
     *     @OA\Response(response=400, description="Error de validación"),
     *     @OA\Response(response=404, description="Rol no encontrado")
     * )
     */

     
    public function updatePartial(Request $request,$id){
        $rol=Rol::find($id);
        if(!$rol){
            $data=[
                "mensage" => " No se encontro rol",
                "status" => 404
            ];
            return response()->json([$data],404);
        }
        $validator = Validator::make($request->all(),[
            "nombreRol" => "min:3|max:30"
        ]);
        if($validator->fails()){
            $data=[
                "mesaje " => "Error al validar rol",
                "errors" => $validator->errors(),
                "status" => 400
            ];
            return response()->json([$data],400);
        }
        if($request->has("idRol")){
            $rol->idRol = $request->idRol;
        }
        if($request->has("nombreRol")){
            $rol->nombreRol = $request->nombreRol;
        }
        $rol->save();
        $data=[
            "rol" => $rol,
            "status" => 200
        ];
        return response()->json([$data],200);
    }
}
