<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\CategoriaHasUsuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class categoriaHasUsuarioController extends Controller
{

    public function index()
    {
        $cathasusu=CategoriaHasUsuario::all();
        if(!$cathasusu){
            return response()->json([
                'mensaje' => 'No retorna por error en DB',
                
                'status' => 400
            ], 400);
        }else{

            $data=[
                "cathasusu" => $cathasusu,
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
            'categoriaVacantesId' => 'required|integer',
            'usuarioNumDocumento' => 'required|integer',
        ]);
    
        if ($validator->fails()) {
            return response()->json([
                'mensaje' => 'Error en la validación de datos de categoriahasusuario',
                'errors' => $validator->errors(),
                'status' => 400
            ], 400);
        }
    
        try {

                $cathasusu  = CategoriaHasUsuario::create([
                    'categoriaVacantesId' => $request->categoriaVacantesId,
                    'usuarioNumDocumento' => $request->usuarioNumDocumento 
                ]);
        
                return response()->json([
                    'mensaje' => 'Categoria has usuario no se ha creado correctamente',
                    'cathasusu' => $cathasusu,
                    'status' => 201
                ], 201);
            
            
        } catch (\Exception $e) {
            return response()->json([
                'mensaje' => 'Error al crear el categoriahasusuario',
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
        $cathasusu = CategoriaHasUsuario::where("usuarioNumDocumento", $id)->get()->toArray();
        if($cathasusu){
            $data=[
                "cathasusu" => $cathasusu,
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
    $cathasusu = CategoriaHasUsuario::where("usuarioNumDocumento", $id)->first();

    if (!$cathasusu) {
        return response()->json([
            "mensaje" => "No se encontró la categoría asignada al usuario",
            "status" => 404
        ], 404);
    }

    $cathasusu->delete();

    return response()->json([
        "mensaje" => "Registro eliminado correctamente",
        "status" => 200
    ], 200);
}

    public function update(Request $request, $id)
    {
        $cathasusu = CategoriaHasUsuario::where("usuarioNumDocumento", $id)->first();
    
        if (!$cathasusu) {
            return response()->json([
                "mensaje" => "No se encontró la categoría asociada al usuario",
                "status" => 404
            ], 404);
        }
    
        $validator = Validator::make($request->all(), [
            'categoriaVacantesId' => 'required|integer',
            'usuarioNumDocumento' => 'required|integer',
        ]);
    
        if ($validator->fails()) {
            return response()->json([
                "errors" => $validator->errors(),
                "status" => 400
            ], 400);
        }
    
        try {
            $cathasusu->categoriaVacantesId = $request->categoriaVacantesId;
            $cathasusu->usuarioNumDocumento = $request->usuarioNumDocumento;
            $cathasusu->save();
    
            return response()->json([
                "mensaje" => "Categoría actualizada correctamente",
                "cathasusu" => $cathasusu,
                "status" => 200
            ], 200);
    
        } catch (\Exception $e) {
            return response()->json([
                "mensaje" => "Error al modificar la categoría del usuario",
                "error" => $e->getMessage(),
                "status" => 500
            ], 500);
        }
    }
        public function updatePartial(Request $request, $id)
    {
        $cathasusu = CategoriaHasUsuario::where("usuarioNumDocumento", $id)->first();

        if (!$cathasusu) {
            $data = [
                "mensage" => " No se encontro la categoria has usuario",
                "status" => 404
            ];
            return response()->json([$data], 404);
        }else{

            $validator = Validator::make($request->all(), [
                'categoriaVacantesId' => 'integer',
                'usuarioNumDocumento' => 'integer',
            ]);
            if ($validator->fails()) {
                $data = [
                    "mesaje " => "Error al validar categoria has usuario",
                    "errors" => $validator->errors(),
                    "status" => 400
                ];
                return response()->json([$data], 400);
            }
            
            $cathasusu->fill($request->all());

            $cathasusu->save();
            $data = [
                "cathasusu" => $cathasusu,
                "status" => 200
            ];
            return response()->json([$data], 200);
        }
    }
}
