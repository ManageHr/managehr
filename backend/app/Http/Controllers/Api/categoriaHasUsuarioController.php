<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\CategoriaHasUsuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

/**
 * @OA\Tag(
 *     name="Categorías Usuario",
 *     description="Gestión de la relación entre categorías de vacantes y usuarios. Permite asignar categorías específicas a usuarios para filtrar vacantes por área de interés."
 * )
 */
class categoriaHasUsuarioController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/categoriaHasUsuario",
     *     summary="Obtener todas las categorías asignadas a usuarios",
     *     tags={"Categorías Usuario"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Lista de categorías de usuarios obtenida exitosamente",
     *         @OA\JsonContent(
     *             @OA\Property(property="cathasusu", type="array", 
     *                 @OA\Items(
     *                     type="object",
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="categoriaVacantesId", type="integer", example=3),
     *                     @OA\Property(property="usuarioNumDocumento", type="integer", example=12345678),
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
     * @OA\Post(
     *     path="/api/categoriaHasUsuario",
     *     summary="Asignar una categoría a un usuario",
     *     tags={"Categorías Usuario"},
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"categoriaVacantesId", "usuarioNumDocumento"},
     *             @OA\Property(property="categoriaVacantesId", type="integer", example=3, description="ID de la categoría de vacantes"),
     *             @OA\Property(property="usuarioNumDocumento", type="integer", example=12345678, description="Número de documento del usuario")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Categoría asignada al usuario correctamente",
     *         @OA\JsonContent(
     *             @OA\Property(property="mensaje", type="string", example="Categoria has usuario no se ha creado correctamente"),
     *             @OA\Property(property="cathasusu", type="object"),
     *             @OA\Property(property="status", type="integer", example=201)
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Error en la validación de datos",
     *         @OA\JsonContent(
     *             @OA\Property(property="mensaje", type="string", example="Error en la validación de datos de categoriahasusuario"),
     *             @OA\Property(property="errors", type="object"),
     *             @OA\Property(property="status", type="integer", example=400)
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Error al crear la asignación",
     *         @OA\JsonContent(
     *             @OA\Property(property="mensaje", type="string", example="Error al crear el categoriahasusuario"),
     *             @OA\Property(property="error", type="string"),
     *             @OA\Property(property="status", type="integer", example=500)
     *         )
     *     )
     * )
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
     * @OA\Get(
     *     path="/api/categoriaHasUsuario/{id}",
     *     summary="Obtener categorías asignadas a un usuario específico",
     *     tags={"Categorías Usuario"},
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
     *         description="Categorías del usuario obtenidas exitosamente",
     *         @OA\JsonContent(
     *             @OA\Property(property="cathasusu", type="array", @OA\Items(type="object")),
     *             @OA\Property(property="status", type="integer", example=200)
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Usuario no existe",
     *         @OA\JsonContent(
     *             @OA\Property(property="mensaje", type="string", example="El usuario no existe"),
     *             @OA\Property(property="status", type="integer", example=400)
     *         )
     *     )
     * )
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
     * @OA\Delete(
     *     path="/api/categoriaHasUsuario/{id}",
     *     summary="Eliminar categoría asignada a un usuario",
     *     tags={"Categorías Usuario"},
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
     *         description="Registro eliminado correctamente",
     *         @OA\JsonContent(
     *             @OA\Property(property="mensaje", type="string", example="Registro eliminado correctamente"),
     *             @OA\Property(property="status", type="integer", example=200)
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Categoría no encontrada",
     *         @OA\JsonContent(
     *             @OA\Property(property="mensaje", type="string", example="No se encontró la categoría asignada al usuario"),
     *             @OA\Property(property="status", type="integer", example=404)
     *         )
     *     )
     * )
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

    /**
     * @OA\Put(
     *     path="/api/categoriaHasUsuario/{id}",
     *     summary="Actualizar categoría asignada a un usuario",
     *     tags={"Categorías Usuario"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Número de documento del usuario",
     *         required=true,
     *         @OA\Schema(type="integer", example=12345678)
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"categoriaVacantesId", "usuarioNumDocumento"},
     *             @OA\Property(property="categoriaVacantesId", type="integer", example=3),
     *             @OA\Property(property="usuarioNumDocumento", type="integer", example=12345678)
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Categoría actualizada correctamente",
     *         @OA\JsonContent(
     *             @OA\Property(property="mensaje", type="string", example="Categoría actualizada correctamente"),
     *             @OA\Property(property="cathasusu", type="object"),
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
     *         description="Categoría no encontrada",
     *         @OA\JsonContent(
     *             @OA\Property(property="mensaje", type="string", example="No se encontró la categoría asociada al usuario"),
     *             @OA\Property(property="status", type="integer", example=404)
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Error al modificar la categoría",
     *         @OA\JsonContent(
     *             @OA\Property(property="mensaje", type="string", example="Error al modificar la categoría del usuario"),
     *             @OA\Property(property="error", type="string"),
     *             @OA\Property(property="status", type="integer", example=500)
     *         )
     *     )
     * )
     */
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
        /**
     * @OA\Patch(
     *     path="/api/categoriaHasUsuario/{id}",
     *     summary="Actualizar parcialmente categoría asignada a un usuario",
     *     tags={"Categorías Usuario"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Número de documento del usuario",
     *         required=true,
     *         @OA\Schema(type="integer", example=12345678)
     *     ),
     *     @OA\RequestBody(
     *         required=false,
     *         @OA\JsonContent(
     *             @OA\Property(property="categoriaVacantesId", type="integer"),
     *             @OA\Property(property="usuarioNumDocumento", type="integer")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Categoría actualizada parcialmente",
     *         @OA\JsonContent(
     *             @OA\Property(property="cathasusu", type="object"),
     *             @OA\Property(property="status", type="integer", example=200)
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Error en la validación",
     *         @OA\JsonContent(
     *             @OA\Property(property="mesaje", type="string", example="Error al validar categoria has usuario"),
     *             @OA\Property(property="errors", type="object"),
     *             @OA\Property(property="status", type="integer", example=400)
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Categoría no encontrada",
     *         @OA\JsonContent(
     *             @OA\Property(property="mensage", type="string", example="No se encontro la categoria has usuario"),
     *             @OA\Property(property="status", type="integer", example=404)
     *         )
     *     )
     * )
     */
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
