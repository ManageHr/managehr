<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Tipopermisos;
use Illuminate\Support\Facades\Validator;

class tipopermisosController extends Controller
{

        /**
     * @OA\Get(
     *     path="/api/tipopermisos",
     *     summary="Listar todos los tipos de permisos",
     *     description="Devuelve una lista completa de los tipos de permisos registrados en el sistema.",
     *     tags={"Tipos de Permisos"},
     *     @OA\Response(
     *         response=200,
     *         description="Lista de tipos de permisos obtenida correctamente",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="tipopermisos", type="array", @OA\Items(
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="nombre", type="string", example="Permiso de salida"),
     *                 @OA\Property(property="descripcion", type="string", example="Permiso para salida personal")
     *             )),
     *             @OA\Property(property="status", type="integer", example=200)
     *         )
     *     )
     * )
     */

    public function index()
    {
        $tipopermiso = Tipopermisos::all();

        $data = [
            "tipopermisos" => $tipopermiso,
            "status" => 200
        ];
        return response()->json($data, 200);
        //return "Obteniendo lista de epss del contepsador";

    }

    public function create()
    {
        //
    }
    /**
     * @OA\Post(
     *     path="/api/tipopermisos",
     *     summary="Crear nuevo tipo de permiso (No permitido)",
     *     description="Este endpoint está deshabilitado. Solo el administrador de base de datos puede crear registros.",
     *     tags={"Tipos de Permisos"},
     *     @OA\Response(
     *         response=400,
     *         description="Operación no permitida para el usuario"
     *     )
     * )
     */

    public function store(Request $request)
    {
        $data = [
            "mesaje " => "este modulo no permite crear, solo el administrador de base de datos lo puede hacer",
            "status" => 400
        ];
        return response()->json([$data], 400);
    }



        /**
     * @OA\Get(
     *     path="/api/tipopermisos/{id}",
     *     summary="Obtener tipo de permiso por ID",
     *     description="Devuelve un tipo de permiso específico por su identificador único.",
     *     tags={"Tipos de Permisos"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID del tipo de permiso",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Tipo de permiso encontrado",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="tipopermisos", type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="nombre", type="string", example="Permiso médico"),
     *                 @OA\Property(property="descripcion", type="string", example="Permiso por cita médica")
     *             ),
     *             @OA\Property(property="status", type="integer", example=200)
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Tipo de permiso no encontrado"
     *     )
     * )
     */

    public function show(string $id)
    {
        $tipopermiso = Tipopermisos::find($id);
        if (!$tipopermiso) {
            $data = [
                "mensage" => " No se encontro eps",
                "status" => 201
            ];
            return response()->json([$data], 201);
        }
        $data = [
            "tipopermisos" => $tipopermiso,
            "status" => 200
        ];
        return response()->json([$data], 200);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        
    }

    /**
     * @OA\Put(
     *     path="/api/tipopermisos/{id}",
     *     summary="Actualizar tipo de permiso (No permitido)",
     *     description="Este endpoint está deshabilitado. Solo el administrador de base de datos puede actualizar registros.",
     *     tags={"Tipos de Permisos"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID del tipo de permiso",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Operación no permitida para el usuario"
     *     )
     * )
     */

    public function update(Request $request, string $id)
    {
        $data = [
            "mesaje " => "este modulo no permite Actualizar, solo el administrador de base de datos lo puede hacer",
            "status" => 400
        ];
        return response()->json([$data], 400);
    }

        /**
     * @OA\Patch(
     *     path="/api/tipopermisos/{id}",
     *     summary="Actualización parcial de tipo de permiso (No permitido)",
     *     description="Este endpoint está deshabilitado. Solo el administrador de base de datos puede actualizar registros.",
     *     tags={"Tipos de Permisos"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID del tipo de permiso",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Operación no permitida para el usuario"
     *     )
     * )
     */

    public function updatePartial(Request $request, $id)
    {
        $data = [
            "mesaje " => "este modulo no permite actualizar, solo el administrador de base de datos lo puede hacer",
            "status" => 400
        ];
        return response()->json([$data], 400);
    }

    /**
     * @OA\Delete(
     *     path="/api/tipopermisos/{id}",
     *     summary="Eliminar tipo de permiso (No permitido)",
     *     description="Este endpoint está deshabilitado. Solo el administrador de base de datos puede eliminar registros.",
     *     tags={"Tipos de Permisos"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID del tipo de permiso",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Operación no permitida para el usuario"
     *     )
     * )
     */

    public function destroy(string $id)
    {
        $data = [
            "mesaje " => "este modulo no permite Actualizar, solo el administrador de base de datos lo puede hacer",
            "status" => 400
        ];
        return response()->json([$data], 400);
    }
}
