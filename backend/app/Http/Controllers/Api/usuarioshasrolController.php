<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Usuarioshasrol;
use Illuminate\Support\Facades\Validator;

class usuarioshasrolController extends Controller
{
    public function index()
    {
        $usuarioshasrol=Usuarioshasrol::all();
        if(!$usuarioshasrol){
            return response()->json([
                'mensaje' => 'No retorna por error en DB',
                
                'status' => 400
            ], 400);
        }else{

            $data=[
                "usuarioshasrol" => $usuarioshasrol,
                "status" => 200
            ];
            return response()->json($data,200);
        }
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

        $validator = Validator::make($request->all(), [
            'estado' => 'required|string|max:10',
            'usuarioNumDocumento' => 'required|integer',
            'rolId' => 'required|integer'
        ]);
    
        if ($validator->fails()) {
            return response()->json([
                'mensaje' => 'Error en la validación de datos de usuariohasrol',
                'errors' => $validator->errors(),
                'status' => 400
            ], 400);
        }
    
        try {
            $user=Usuarioshasrol::where("usuarioNumDocumento",$request->usuarioNumDocumento)->first();
            if($user){
                return response()->json([
                    'mensaje' => 'El usuario ya esta registrado',
                    'status' => 400
                ], 500);
            }else{
                $usuarioshasrol = Usuarioshasrol::create([
                    'estado' => $request->estado,
                    'usuarioNumDocumento' => $request->usuarioNumDocumento,
                    'rolId' => $request->rolId
                    
                ]);
        
                return response()->json([
                    'mensaje' => 'usuariohasrol creado correctamente',
                    'usuarioshasrol' => $usuarioshasrol,
                    'status' => 201
                ], 201);
            }
            
        } catch (\Exception $e) {
            return response()->json([
                'mensaje' => 'Error al crear el usuariohasrol',
                'error' => $e->getMessage(),
                'status' => 500
            ], 500);
        }
        
        
        
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $user=Usuarioshasrol::where("usuarioNumDocumento",$id)->first();
        if($user){
            $data=[
                "usuarioshasrol" => $user,
                "status" => 200
            ];
            return response()->json($data,200);
        }else{
            return response()->json([
                'mensaje' => 'El usuario no existe',
                'status' => 400
            ], 500);
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function destroy($id)
    {
        
        $usuarioshasrol = Usuarioshasrol::where("usuarioNumDocumento",$id)->first();
        if (!$usuarioshasrol) {
            $data = [
                "mensage" => " No se encontro usuarioshasrol",
                "status" => 404
            ];
            return response()->json([$data], 404);
        }else{
            $usuarioshasrol->delete();
            $data = [
                "usuarioshasrol" => 'usuariohasrol eliminado',
                "status" => 200
            ];
            return response()->json([$data], 200);
        }
        
    }
    public function update(Request $request, $id)
    {
        $usuarioshasrol = Usuarioshasrol::where("usuarioNumDocumento",$id)->first();
        if (!$usuarioshasrol) {
            $data = [
                "mensage" => " No se encontro usuarioshasrol",
                "status" => 404
            ];
            return response()->json([$data], 404);
        }else{
            
            $validator = Validator::make($request->all(), [
                'estado' => 'required|string|max:10',
                'usuarioNumDocumento' => 'required|integer',
                'rolId' => 'required|integer'
            ]);
            if ($validator->fails()) {
                $data = [
                    "errors" => $validator->errors(),
                    "status" => 400
                ];
                return response()->json([$data], 400);
            }
            
            $usuarioshasrol->estado = $request->estado;
            $usuarioshasrol->usuarioNumDocumento = $request->usuarioNumDocumento;
            $usuarioshasrol->rolId = $request->rolId;
            
    
            try {
                $usuarioshasrol->save();
                $data = [
                    "usuarioshasrol" => $usuarioshasrol,
                    "status" => 200
                ];
                return response()->json([$data], 200);
            } catch (\Exception $e) {
                return response()->json([
                    "mensaje" => "Error al modificar el usuario has rol",
                    "error" => $e->getMessage(),
                    "status" => 500
                ], 500);
            }
        }
    }
    public function updatePartial(Request $request, $id)
    {
        $usuarioshasrol = Usuarioshasrol::where("usuarioNumDocumento",$id)->first();
        if (!$usuarioshasrol) {
            $data = [
                "mensage" => " No se encontro usuarioshasrol",
                "status" => 404
            ];
            return response()->json([$data], 404);
        }else{

            $validator = Validator::make($request->all(), [
                
                'estado' => 'string|max:10',
                'usuarioNumDocumento' => 'integer',
                'rolId' => 'integer'
            ]);
            if ($validator->fails()) {
                $data = [
                    "mesaje " => "Error al validar usuariohasrol",
                    "errors" => $validator->errors(),
                    "status" => 400
                ];
                return response()->json([$data], 400);
            }
            if ($request->has("estado")) {
                $usuarioshasrol->estado = $request->estado;
            }
            if ($request->has("usuarioNumDocumento")) {
                $usuarioshasrol->usuarioNumDocumento = $request->usuarioNumDocumento;
            }
            if ($request->has("rolId")) {
                $usuarioshasrol->rolId = $request->rolId;
            }
            
            
            $usuarioshasrol->save();
            $data = [
                "usuarioshasrol" => $usuarioshasrol,
                "status" => 200
            ];
            return response()->json([$data], 200);
        }
    }
}
