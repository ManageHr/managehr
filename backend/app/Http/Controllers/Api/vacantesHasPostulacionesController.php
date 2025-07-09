<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\VacantesHasPostulaciones;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

/**
 * @OA\Tag(
 *     name="Vacantes Postulaciones",
 *     description="Gestión de la relación entre vacantes y postulaciones. Permite vincular postulaciones específicas a vacantes y administrar estas relaciones."
 * )
 */
class vacantesHasPostulacionesController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/vacantes-has-postulaciones",
     *     summary="Obtener todas las relaciones vacantes-postulaciones",
     *     tags={"Vacantes Postulaciones"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Lista de relaciones vacantes-postulaciones obtenida exitosamente",
     *         @OA\JsonContent(
     *             @OA\Property(property="vachaspos", type="array", 
     *                 @OA\Items(
     *                     type="object",
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="vacantesid", type="integer", example=5),
     *                     @OA\Property(property="postulacionesid", type="integer", example=10),
     *                     @OA\Property(property="created_at", type="string", format="date-time"),
     *                     @OA\Property(property="updated_at", type="string", format="date-time")
     *                 )
     *             ),
     *             @OA\Property(property="status", type="integer", example=200)
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Error en la base de datos",
     *         @OA\JsonContent(
     *             @OA\Property(property="mensaje", type="string", example="No retorna por error en DB"),
     *             @OA\Property(property="status", type="integer", example=400)
     *         )
     *     )
     * )
     */
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
     * @OA\Post(
     *     path="/api/vacantes-has-postulaciones",
     *     summary="Crear relación entre vacante y postulación",
     *     tags={"Vacantes Postulaciones"},
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"vacantesid", "postulacionesid"},
     *             @OA\Property(property="vacantesid", type="integer", example=5, description="ID de la vacante"),
     *             @OA\Property(property="postulacionesid", type="integer", example=10, description="ID de la postulación")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Relación vacante-postulación creada correctamente",
     *         @OA\JsonContent(
     *             @OA\Property(property="mensaje", type="string", example="Vacante has postulacion no se ha creado correctamente"),
     *             @OA\Property(property="vachaspos", type="object"),
     *             @OA\Property(property="status", type="integer", example=201)
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Error en la validación de datos",
     *         @OA\JsonContent(
     *             @OA\Property(property="mensaje", type="string", example="Error en la validación de datos de vacanteshaspostulaciones"),
     *             @OA\Property(property="errors", type="object"),
     *             @OA\Property(property="status", type="integer", example=400)
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Error al crear la relación",
     *         @OA\JsonContent(
     *             @OA\Property(property="mensaje", type="string", example="Error al crear el vacantehaspostulacion"),
     *             @OA\Property(property="error", type="string"),
     *             @OA\Property(property="status", type="integer", example=500)
     *         )
     *     )
     * )
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
     * @OA\Get(
     *     path="/api/vacanteshaspostulaciones/{id}",
     *     summary="Obtener postulaciones de una vacante específica",
     *     tags={"Vacantes Postulaciones"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID de la vacante",
     *         required=true,
     *         @OA\Schema(type="integer", example=5)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Postulaciones de la vacante obtenidas exitosamente",
     *         @OA\JsonContent(
     *             @OA\Property(property="usuarioshasrol", type="array", @OA\Items(type="object")),
     *             @OA\Property(property="status", type="integer", example=200)
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Vacante no existe",
     *         @OA\JsonContent(
     *             @OA\Property(property="mensaje", type="string", example="La vacante no existe"),
     *             @OA\Property(property="status", type="integer", example=400)
     *         )
     *     )
     * )
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
     * @OA\Delete(
     *     path="/api/vacanteshaspostulaciones/{id}",
     *     summary="Eliminar relación vacante-postulación",
     *     tags={"Vacantes Postulaciones"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Número de documento del usuario",
     *         required=true,
     *         @OA\Schema(type="integer", example=12345678)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Relación eliminada correctamente",
     *         @OA\JsonContent(
     *             @OA\Property(property="vachaspos", type="string", example="Vacante has postulacion eliminado"),
     *             @OA\Property(property="status", type="integer", example=200)
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Relación no encontrada",
     *         @OA\JsonContent(
     *             @OA\Property(property="mensage", type="string", example="No se encontro usuarioshasrol"),
     *             @OA\Property(property="status", type="integer", example=404)
     *         )
     *     )
     * )
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
    /**
     * @OA\Put(
     *     path="/api/vacanteshaspostulaciones/{id}",
     *     summary="Actualizar relación vacante-postulación",
     *     tags={"Vacantes Postulaciones"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID de la vacante",
     *         required=true,
     *         @OA\Schema(type="integer", example=5)
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"vacantesid", "postulacionesid"},
     *             @OA\Property(property="vacantesid", type="integer", example=5),
     *             @OA\Property(property="postulacionesid", type="integer", example=10)
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Relación actualizada correctamente",
     *         @OA\JsonContent(
     *             @OA\Property(property="usuarioshasrol", type="array", @OA\Items(type="object")),
     *             @OA\Property(property="status", type="integer", example=200)
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Error en la validación",
     *         @OA\JsonContent(
     *             @OA\Property(property="errors", type="object"),
     *             @OA\Property(property="status", type="integer", example=400)
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Relación no encontrada",
     *         @OA\JsonContent(
     *             @OA\Property(property="mensage", type="string", example="No se encontro la vacante has postulacion"),
     *             @OA\Property(property="status", type="integer", example=404)
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Error al modificar la relación",
     *         @OA\JsonContent(
     *             @OA\Property(property="mensaje", type="string", example="Error al modificar el vacantes has postulaciones"),
     *             @OA\Property(property="error", type="string"),
     *             @OA\Property(property="status", type="integer", example=500)
     *         )
     *     )
     * )
     */
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
    /**
     * @OA\Patch(
     *     path="/api/vacanteshaspostulaciones/{id}",
     *     summary="Actualizar parcialmente relación vacante-postulación",
     *     tags={"Vacantes Postulaciones"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID de la vacante",
     *         required=true,
     *         @OA\Schema(type="integer", example=5)
     *     ),
     *     @OA\RequestBody(
     *         required=false,
     *         @OA\JsonContent(
     *             @OA\Property(property="vacantesid", type="integer"),
     *             @OA\Property(property="postulacionesid", type="integer")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Relación actualizada parcialmente",
     *         @OA\JsonContent(
     *             @OA\Property(property="vachaspos", type="object"),
     *             @OA\Property(property="status", type="integer", example=200)
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Error en la validación",
     *         @OA\JsonContent(
     *             @OA\Property(property="mesaje", type="string", example="Error al validar vacantes has postulacion"),
     *             @OA\Property(property="errors", type="object"),
     *             @OA\Property(property="status", type="integer", example=400)
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Relación no encontrada",
     *         @OA\JsonContent(
     *             @OA\Property(property="mensage", type="string", example="No se encontro la vacantes has postulaciones"),
     *             @OA\Property(property="status", type="integer", example=404)
     *         )
     *     )
     * )
     */
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
