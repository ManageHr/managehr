<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Usuarioshasrol;
use Illuminate\Support\Facades\Validator;

/**
 * @OA\Tag(
 *     name="Usuarios Roles",
 *     description="Gestión de la asignación de roles a usuarios. Permite administrar qué roles tiene cada usuario en el sistema y su estado de activación."
 * )
 */
class usuarioshasrolController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/usuarios-has-rol",
     *     summary="Obtener todas las asignaciones de roles a usuarios",
     *     tags={"Usuarios Roles"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Lista de asignaciones de roles obtenida exitosamente",
     *         @OA\JsonContent(
     *             @OA\Property(property="usuarioshasrol", type="array", 
     *                 @OA\Items(
     *                     type="object",
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="estado", type="string", example="Activo"),
     *                     @OA\Property(property="usuarioNumDocumento", type="integer", example=12345678),
     *                     @OA\Property(property="rolId", type="integer", example=2),
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
     * @OA\Post(
     *     path="/api/usuarios-has-rol",
     *     summary="Asignar un rol a un usuario",
     *     tags={"Usuarios Roles"},
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"estado", "usuarioNumDocumento", "rolId"},
     *             @OA\Property(property="estado", type="string", example="Activo", description="Estado del rol (Activo/Inactivo)"),
     *             @OA\Property(property="usuarioNumDocumento", type="integer", example=12345678, description="Número de documento del usuario"),
     *             @OA\Property(property="rolId", type="integer", example=2, description="ID del rol a asignar")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Rol asignado al usuario correctamente",
     *         @OA\JsonContent(
     *             @OA\Property(property="mensaje", type="string", example="usuariohasrol creado correctamente"),
     *             @OA\Property(property="usuarioshasrol", type="object"),
     *             @OA\Property(property="status", type="integer", example=201)
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Error en la validación de datos o usuario ya registrado",
     *         @OA\JsonContent(
     *             @OA\Property(property="mensaje", type="string", example="Error en la validación de datos de usuariohasrol"),
     *             @OA\Property(property="errors", type="object"),
     *             @OA\Property(property="status", type="integer", example=400)
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Usuario ya registrado o error al crear",
     *         @OA\JsonContent(
     *             @OA\Property(property="mensaje", type="string", example="El usuario ya esta registrado"),
     *             @OA\Property(property="status", type="integer", example=400)
     *         )
     *     )
     * )
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
     * @OA\Get(
     *     path="/api/usuarios-has-rol/{id}",
     *     summary="Obtener rol asignado a un usuario específico",
     *     tags={"Usuarios Roles"},
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
     *         description="Rol del usuario obtenido exitosamente",
     *         @OA\JsonContent(
     *             @OA\Property(property="usuarioshasrol", type="object"),
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
     * @OA\Put(
     *     path="/api/usuarios-has-rol/{id}",
     *     summary="Actualizar rol asignado a un usuario",
     *     tags={"Usuarios Roles"},
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
     *             required={"estado", "usuarioNumDocumento", "rolId"},
     *             @OA\Property(property="estado", type="string", example="Activo"),
     *             @OA\Property(property="usuarioNumDocumento", type="integer", example=12345678),
     *             @OA\Property(property="rolId", type="integer", example=2)
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Rol actualizado correctamente",
     *         @OA\JsonContent(
     *             @OA\Property(property="usuarioshasrol", type="object"),
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
     *         description="Usuario no encontrado",
     *         @OA\JsonContent(
     *             @OA\Property(property="mensage", type="string", example="No se encontro usuarioshasrol"),
     *             @OA\Property(property="status", type="integer", example=404)
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Error al modificar el rol",
     *         @OA\JsonContent(
     *             @OA\Property(property="mensaje", type="string", example="Error al modificar el usuario has rol"),
     *             @OA\Property(property="error", type="string"),
     *             @OA\Property(property="status", type="integer", example=500)
     *         )
     *     )
     * )
     */
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

    /**
     * @OA\Patch(
     *     path="/api/usuarios-has-rol/{id}",
     *     summary="Actualizar parcialmente rol asignado a un usuario",
     *     tags={"Usuarios Roles"},
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
     *             @OA\Property(property="estado", type="string"),
     *             @OA\Property(property="usuarioNumDocumento", type="integer"),
     *             @OA\Property(property="rolId", type="integer")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Rol actualizado parcialmente",
     *         @OA\JsonContent(
     *             @OA\Property(property="usuarioshasrol", type="object"),
     *             @OA\Property(property="status", type="integer", example=200)
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Error en la validación",
     *         @OA\JsonContent(
     *             @OA\Property(property="mesaje", type="string", example="Error al validar usuariohasrol"),
     *             @OA\Property(property="errors", type="object"),
     *             @OA\Property(property="status", type="integer", example=400)
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Usuario no encontrado",
     *         @OA\JsonContent(
     *             @OA\Property(property="mensage", type="string", example="No se encontro usuarioshasrol"),
     *             @OA\Property(property="status", type="integer", example=404)
     *         )
     *     )
     * )
     */
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

    /**
     * @OA\Delete(
     *     path="/api/usuarios-has-rol/{id}",
     *     summary="Eliminar rol asignado a un usuario",
     *     tags={"Usuarios Roles"},
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
     *         description="Rol eliminado correctamente",
     *         @OA\JsonContent(
     *             @OA\Property(property="usuarioshasrol", type="string", example="usuariohasrol eliminado"),
     *             @OA\Property(property="status", type="integer", example=200)
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Usuario no encontrado",
     *         @OA\JsonContent(
     *             @OA\Property(property="mensage", type="string", example="No se encontro usuarioshasrol"),
     *             @OA\Property(property="status", type="integer", example=404)
     *         )
     *     )
     * )
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
}
