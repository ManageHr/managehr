<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Area;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class areaController extends Controller
{
    public function index()
    {
        $areas = Area::all();
        return response()->json(["areas" => $areas, "status" => 200], 200);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "nombreArea" => "required|string|max:50",
            "jefePersonal" => "required|string|max:100",
            "idJefe" => "required|integer",
            "estado" => "required|integer",
        ]);

        if ($validator->fails()) {
            return response()->json([
                "mensaje" => "Error en la validación del área",
                "errors" => $validator->errors(),
                "status" => 400
            ], 400);
        }

        try {
            $area = Area::create($request->all());
            return response()->json([
                "mensaje" => "Área creada correctamente",
                "area" => $area,
                "status" => 201
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                "mensaje" => "Error al crear el área",
                "error" => $e->getMessage(),
                "status" => 500
            ], 500);
        }
    }

    public function show($id)
    {
        $area = Area::find($id);
        if (!$area) {
            return response()->json([
                "mensaje" => "No se encontró el área",
                "status" => 404
            ], 404);
        }
        return response()->json(["area" => $area, "status" => 200], 200);
    }
    public function showNombre($id)
    {
        $area = Area::where('nombreArea', $id)->get();
        
        if (!$area) {
            return response()->json([
                "mensaje" => "No se encontró el área",
                "status" => 404
            ], 404);
        }
        return response()->json(["area" => $area, "status" => 200], 200);
    }

    public function update(Request $request, $id)
    {
        $area = Area::find($id);
        if (!$area) {
            return response()->json([
                "mensaje" => "No se encontró el área",
                "status" => 404
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            "nombreArea" => "required|string|max:50",
            "jefePersonal" => "required|string|max:100",
            "idJefe" => "required|integer",
            "estado" => "required|integer",
            
        ]);

        if ($validator->fails()) {
            return response()->json([
                "mensaje" => "Error en la validación del área",
                "errors" => $validator->errors(),
                "status" => 400
            ], 400);
        }

        try {
            $area->update($request->all());
            return response()->json(["area" => $area, "status" => 200], 200);
        } catch (\Exception $e) {
            return response()->json([
                "mensaje" => "Error al actualizar el área",
                "error" => $e->getMessage(),
                "status" => 500
            ], 500);
        }
    }

    public function destroy($id)
    {
        $area = Area::find($id);
        if (!$area) {
            return response()->json([
                "mensaje" => "No se encontró el área",
                "status" => 404
            ], 404);
        }

        $area->delete();
        return response()->json([
            "mensaje" => "Área eliminada correctamente",
            "status" => 200
        ], 200);
    }
    
    public function updatePartial(Request $request, $id)
    {
        $area = Area::find($id);
        if (!$area) {
            $data = [
                "mensage" => " No se encontro Area",
                "status" => 404
            ];
            return response()->json([$data], 404);
        }
        $validator = Validator::make($request->all(), [
            "nombreArea" => "string|max:50",
            "jefePersonal" => "string|max:100",
            "idJefe" => "integer",
            "estado" => "integer",
        ]);
        if ($validator->fails()) {
            $data = [
                "mesaje " => "Error al validar genero",
                "errors" => $validator->errors(),
                "status" => 400
            ];
            return response()->json([$data], 400);
        }
        if ($request->has("estado")) {
            $area->estado = $request->estado;
        }
        if ($request->has("nombreArea")) {
            $area->nombreArea = $request->nombreArea;
        }
        if ($request->has("jefePersonal")) {
            $area->jefePersonal = $request->jefePersonal;
        }
        $area->save();
        $data = [
            "genero" => $area,
            "status" => 200
        ];
        return response()->json([$data], 200);
    }
}
