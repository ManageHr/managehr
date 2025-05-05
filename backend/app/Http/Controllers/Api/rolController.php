<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Rol;
use Illuminate\Support\Facades\Validator;

class rolController extends Controller
{
    public function index(){
        $rols=Rol::all();
        $data=[
            "rol" => $rols,
            "status" => 200
        ];
        return response()->json($data,200);
    }
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
