<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Pensiones;
use Illuminate\Support\Facades\Validator;


/**
 * @OA\Tag(
 *     name="Pensiones",
 *     description="Gestión de las entidades de pensiones"
 */

/**
 * @OA\SecurityScheme(
 *     type="http",
 *     description="Autenticación mediante JWT. Agrega 'Bearer {token}'",
 *     name="Authorization",
 *     in="header",
 *     scheme="bearer",
 *     bearerFormat="JWT",
 *     securityScheme="bearerAuth"
 * )
 */

class pensionesController extends Controller
{

    /**
     * @OA\Get(
     *     path="/api/pensiones",
     *     summary="Listar todas las pensiones",
     *     tags={"Pensiones"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Lista de pensiones",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="pensiones", type="array", @OA\Items(type="object")),
     *             @OA\Property(property="status", type="integer", example=200)
     *         )
     *     )
     * )
     */

    public function index()
    {

        $pensiones = Pensiones::orderBy('nombrePensiones', 'asc')->get();

        $data = [
            "pensiones" => $pensiones,
            "status" => 200
        ];
        return response()->json($data, 200);


    }

       /**
     * @OA\Post(
     *     path="/api/pensiones",
     *     summary="Intentar crear una pensión (no permitido)",
     *     tags={"Pensiones"},
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="nombrePensiones", type="string")
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Creación no permitida"
     *     )
     * )
     */

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
        $data = [
            "mesaje " => "este modulo no permite crear, solo el administrador de base de datos lo puede hacer",
            "status" => 400
        ];
        return response()->json([$data], 400);
    }


     /**
     * @OA\Get(
     *     path="/api/pensiones/{id}",
     *     summary="Mostrar una pensión específica",
     *     tags={"Pensiones"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID de la pensión",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Pensión encontrada",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="pensiones", type="object"),
     *             @OA\Property(property="status", type="integer", example=200)
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="No se encontró la pensión"
     *     )
     * )
     */

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $pensiones = Pensiones::find($id);
        if (!$pensiones) {
            $data = [
                "mensage" => " No se encontro pension",
                "status" => 201
            ];
            return response()->json([$data], 201);
        }
        $data = [
            "pensiones" => $pensiones,
            "status" => 200
        ];
        return response()->json([$data], 200);
    }


    /**
     * @OA\Put(
     *     path="/api/pensiones/{id}",
     *     summary="Intentar actualizar una pensión (no permitido)",
     *     tags={"Pensiones"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID de la pensión",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="nombrePensiones", type="string")
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Actualización no permitida"
     *     )
     * )
     */

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {

    }

    /**
     * Update the specified resource in storage.
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
     *     path="/api/pensiones/{id}",
     *     summary="Intentar actualización parcial (no permitido)",
     *     tags={"Pensiones"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID de la pensión",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Actualización no permitida"
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
     *     path="/api/pensiones/{id}",
     *     summary="Intentar eliminar una pensión (no permitido)",
     *     tags={"Pensiones"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID de la pensión",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Eliminación no permitida"
     *     )
     * )
     */

    /**
     * Remove the specified resource from storage.
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
