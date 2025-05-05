<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Pazysalvo;
use Illuminate\Support\Facades\Validator;

class pazysalvoController extends Controller
{
    public function index()
    {
        $pazysalvo=Pazysalvo::all();
        $data=[
            "pazysalvo" => $pazysalvo,
            "status" => 200
        ];
        return response()->json($data,200);
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
            'descrip' => 'required|string|max:500',
            'firma' => 'required|string|max:50',
            'contratoId' => 'required|integer'
        ]);
    
        if ($validator->fails()) {
            return response()->json([
                'mensaje' => 'Error en la validación de datos de la paz y salvo',
                'errors' => $validator->errors(),
                'status' => 400
            ], 400);
        }
    
        try {
            $pazysalvo = Pazysalvo::create([
                'descrip' => $request->descrip,
                'firma' => $request->firma,
                'contratoId' => $request->contratoId
                
            ]);
    
            return response()->json([
                'mensaje' => 'paz y salvo creado correctamente',
                'pazysalvo' => $pazysalvo,
                'status' => 201
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'mensaje' => 'Error al crear el paz y salvo',
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
        $pazysalvo=Pazysalvo::find($id);
        $data=[
            "pazysalvo" => $pazysalvo,
            "status" => 200
        ];
        return response()->json($data,200);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function destroy($id)
    {
        $pazysalvo = Pazysalvo::find($id);
        if (!$pazysalvo) {
            $data = [
                "mensage" => " No se encontro pazysalvo",
                "status" => 404
            ];
            return response()->json([$data], 404);
        }
        $pazysalvo->delete();
        $data = [
            "pazysalvo" => 'paz y salvo eliminado',
            "status" => 200
        ];
        return response()->json([$data], 200);
    }
    public function update(Request $request, $id)
    {
        $pazysalvo = Pazysalvo::find($id);
        if (!$pazysalvo) {
            $data = [
                "mensage" => " No se encontro pazysalvo",
                "status" => 404
            ];
            return response()->json([$data], 404);
        }
        $validator = Validator::make($request->all(), [
            
            'descrip' => 'required|string|max:500',
            'firma' => 'required|string|max:50',
            'contratoId' => 'required|integer'
        ]);
        if ($validator->fails()) {
            $data = [
                "errors" => $validator->errors(),
                "status" => 400
            ];
            return response()->json([$data], 400);
        }
        
        $pazysalvo->descrip = $request->descrip;
        $pazysalvo->firma = $request->firma;
        $pazysalvo->contratoId = $request->contratoId;

        try {
            $pazysalvo->save();
            $data = [
                "pazysalvo" => $pazysalvo,
                "status" => 200
            ];
            return response()->json([$data], 200);
        } catch (\Exception $e) {
            return response()->json([
                "mensaje" => "Error al modificar la paz y salvo",
                "error" => $e->getMessage(),
                "status" => 500
            ], 500);
        }
    }
    public function updatePartial(Request $request, $id)
    {
        $pazysalvo = Pazysalvo::find($id);
        if (!$pazysalvo) {
            $data = [
                "mensage" => " No se encontro pazysalvo",
                "status" => 404
            ];
            return response()->json([$data], 404);
        }
        $validator = Validator::make($request->all(), [
            
            'descrip' => 'string|max:500',
            'firma' => 'string|max:50',
            'contratoId' => 'integer',
        ]);
        if ($validator->fails()) {
            $data = [
                "mesaje " => "Error al validar paz y salvo",
                "errors" => $validator->errors(),
                "status" => 400
            ];
            return response()->json([$data], 400);
        }
        if ($request->has("descrip")) {
            $pazysalvo->descrip = $request->descrip;
        }
        if ($request->has("firma")) {
            $pazysalvo->firma = $request->firma;
        }
        if ($request->has("contratoId")) {
            $pazysalvo->contratoId = $request->contratoId;
        }
        
        
        $pazysalvo->save();
        $data = [
            "pazysalvo" => $pazysalvo,
            "status" => 200
        ];
        return response()->json([$data], 200);
    }
}
