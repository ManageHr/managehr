<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Permisos;
use Illuminate\Support\Facades\Validator;

class permisosController extends Controller
{
    public function index()
    {
        $permisos = Permisos::all();
        if (!$permisos) {
            return response()->json([
                'mensaje' => 'No retorna por error en DB',
                'errors' => $permisos->errors(),
                'status' => 400
            ], 400);
        } else {

            $data = [
                "permisos" => $permisos,
                "status" => 200
            ];
            return response()->json($data, 200);
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
            'descrip' => 'required|string|max:500',
            'fechaInicio' => 'required|date',
            'fechaFinal' => 'required|date',
            'estado' => 'required|integer',
            'tipoPermisoId' => 'required|integer',
            'contratoId' => 'required|integer'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'mensaje' => 'Error en la validación de datos de permisos',
                'errors' => $validator->errors(),
                'status' => 400
            ], 400);
        }

        try {


            $permisos = Permisos::create([
                'descrip' => $request->descrip,
                'fechaInicio' => $request->fechaInicio,
                'fechaFinal' => $request->fechaFinal,
                'estado' => $request->estado,
                'tipoPermisoId' => $request->tipoPermisoId,
                'contratoId' => $request->contratoId

            ]);

            return response()->json([
                'mensaje' => 'usuariohasrol creado correctamente',
                'permisos' => $permisos,
                'status' => 201
            ], 201);


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
        $user = Permisos::find($id);
        if ($user) {
            $data = [
                "permisos" => $user,
                "status" => 200
            ];
            return response()->json($data, 200);
        } else {
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

        $permisos = Permisos::find($id);
        if (!$permisos) {
            $data = [
                "mensage" => " No se encontro permisos",
                "status" => 404
            ];
            return response()->json([$data], 404);
        } else {
            $permisos->delete();
            $data = [
                "permisos" => 'usuariohasrol eliminado',
                "status" => 200
            ];
            return response()->json([$data], 200);
        }

    }
    public function update(Request $request, $id)
    {
        $permisos = Permisos::where("usuarioNumDocumento", $id)->first();
        if (!$permisos) {
            $data = [
                "mensage" => " No se encontro permisos",
                "status" => 404
            ];
            return response()->json([$data], 404);
        } else {

            $validator = Validator::make($request->all(), [
                'descrip' => 'required|string|max:500',
                'fechaInicio' => 'required|date',
                'fechaFinal' => 'required|date',
                'estado' => 'required|integer',
                'tipoPermisoId' => 'required|integer',
                'contratoId' => 'required|integer'
            ]);
            if ($validator->fails()) {
                $data = [
                    "errors" => $validator->errors(),
                    "status" => 400
                ];
                return response()->json([$data], 400);
            }

            $permisos->descrip=$request->descrip;
            $permisos->fechaInicio = $request->fechaInicio;
            $permisos->fechaFinal = $request->fechaFinal;
            $permisos->estado = $request->estado;
            $permisos->tipoPermisoId = $request->tipoPermisoId;
            $permisos->contratoId = $request->contratoId;
            

            try {
                $permisos->save();
                $data = [
                    "permisos" => $permisos,
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
        $permisos = Permisos::where("usuarioNumDocumento", $id)->first();
        if (!$permisos) {
            $data = [
                "mensage" => " No se encontro permisos",
                "status" => 404
            ];
            return response()->json([$data], 404);
        } else {

            $validator = Validator::make($request->all(), [

                'descrip' => 'string|max:500',
                'fechaInicio' => 'date',
                'fechaFinal' => 'date',
                'estado' => 'integer',
                'tipoPermisoId' => 'integer',
                'contratoId' => 'integer'
            ]);
            if ($validator->fails()) {
                $data = [
                    "mesaje " => "Error al validar usuariohasrol",
                    "errors" => $validator->errors(),
                    "status" => 400
                ];
                return response()->json([$data], 400);
            }
            $permisos->descrip=$request->descrip;
            $permisos->fechaInicio = $request->fechaInicio;
            $permisos->fechaFinal = $request->fechaFinal;
            $permisos->estado = $request->estado;
            $permisos->tipoPermisoId = $request->tipoPermisoId;
            $permisos->contratoId = $request->contratoId;

            if ($request->has("estado")) {
                $permisos->estado = $request->estado;
            }
            if ($request->has("fechaInicio")) {
                $permisos->fechaInicio = $request->fechaInicio;
            }
            if ($request->has("fechaFinal")) {
                $permisos->fechaFinal = $request->fechaFinal;
            }
            if ($request->has("topoPermisoId")) {
                $permisos->topoPermisoId = $request->topoPermisoId;
            }
            if ($request->has("contratoId")) {
                $permisos->contratoId = $request->contratoId;
            }


            $permisos->save();
            $data = [
                "permisos" => $permisos,
                "status" => 200
            ];
            return response()->json([$data], 200);
        }
    }
}
