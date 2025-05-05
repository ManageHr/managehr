<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\VacantesHasPostulaciones;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class vacantesHasPostulacionesController extends Controller
{
 public function index()
    {
        $vachaspos=VacantesHasPostulaciones::all();
        if(!$vachaspos){
            return response()->json([
                'mensaje' => 'No retorna por error en DB',
                
                'status' => 400
            ], 400);
        }else{

            $data=[
                "vachaspos" => $vachaspos,
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
            'vacantesid' => 'required|integer',
            'postulacionesid' => 'required|integer',
        ]);
    
        if ($validator->fails()) {
            return response()->json([
                'mensaje' => 'Error en la validación de datos de vacanteshaspostulaciones',
                'errors' => $validator->errors(),
                'status' => 400
            ], 400);
        }
    
        try {

                $vachaspos = VacantesHasPostulaciones::create([
                    'vacantesid' => $request->vacantesid,
                    'postulacionesid' => $request->postulacionesid
                ]);
        
                return response()->json([
                    'mensaje' => 'Vacante has postulacion no se ha creado correctamente',
                    'vachaspos' => $vachaspos,
                    'status' => 201
                ], 201);
            
            
        } catch (\Exception $e) {
            return response()->json([
                'mensaje' => 'Error al crear el vacantehaspostulacion',
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
        $vachaspos = VacantesHasPostulaciones::where("vacantesid", $id)->get()->toArray();
        if($vachaspos){
            $data=[
                "usuarioshasrol" => $vachaspos,
                "status" => 200
            ];
            return response()->json($data,200);
        }else{
            return response()->json([
                'mensaje' => 'La vacante no existe',
                'status' => 400
            ], 500);
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function destroy($id)
    {
        
        $vachaspos = VacantesHasPostulaciones::where("usuarioNumDocumento",$id)->first();
        if (!$vachaspos) {
            $data = [
                "mensage" => " No se encontro usuarioshasrol",
                "status" => 404
            ];
            return response()->json([$data], 404);
        }else{
            foreach($vachaspos as $temp){

                $temp->delete();
            }
            $data = [
                "vachaspos" => 'Vacante has postulacion eliminado',
                "status" => 200
            ];
            return response()->json([$data], 200);
        }
        
    }
    public function update(Request $request, $id)
    {
        $vachaspos = VacantesHasPostulaciones::where("vacantesid", $id)->get()->toArray();
        if (!$vachaspos) {
            $data = [
                "mensage" => " No se encontro la vacante has postulacion",
                "status" => 404
            ];
            return response()->json([$data], 404);
        }else{
            
            $validator = Validator::make($request->all(), [
                'vacantesid' => 'required|integer',
                'postulacionesid' => 'required|integer'
            ]);
            if ($validator->fails()) {
                $data = [
                    "errors" => $validator->errors(),
                    "status" => 400
                ];
                return response()->json([$data], 400);
            }
            
            $vachaspos->vacantesid = $request->vacantesid;
            $vachaspos->postulacionesid = $request->postulacionesid;
    
            try {
                foreach($vachaspos as $temp){

                    $temp->save();
                }
                $data = [
                    "usuarioshasrol" => $vachaspos,
                    "status" => 200
                ];
                return response()->json([$data], 200);
            } catch (\Exception $e) {
                return response()->json([
                    "mensaje" => "Error al modificar el vacantes has postulaciones",
                    "error" => $e->getMessage(),
                    "status" => 500
                ], 500);
            }
        }
    }
    public function updatePartial(Request $request, $id)
    {
        $vachaspos = VacantesHasPostulaciones::where("vacantesid", $id)->get()->toArray();
        if (!$vachaspos) {
            $data = [
                "mensage" => " No se encontro la vacantes has postulaciones",
                "status" => 404
            ];
            return response()->json([$data], 404);
        }else{

            $validator = Validator::make($request->all(), [
                 'vacantesid' => 'required|integer',
                'postulacionesid' => 'required|integer'
            ]);
            if ($validator->fails()) {
                $data = [
                    "mesaje " => "Error al validar vacantes has postulacion",
                    "errors" => $validator->errors(),
                    "status" => 400
                ];
                return response()->json([$data], 400);
            }
            
            $vachaspos->fill($request->all());

            $vachaspos->save();
            $data = [
                "vachaspos" => $vachaspos,
                "status" => 200
            ];
            return response()->json([$data], 200);
        }
    }
}
