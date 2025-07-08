<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Tipodocumento;
use Illuminate\Http\Request;

/**
 * @OA\Tag(
 *     name="Tipos de Documento",
 *     description="Gestión de Tipos de Documento"
 * )
 *
 * @OA\SecurityScheme(
 *     securityScheme="bearerAuth",
 *     type="http",
 *     scheme="bearer",
 *     bearerFormat="JWT"
 * )
 */

class tipodocumentoController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/tipodocumento",
     *     summary="Listar todos los tipos de documento",
     *     tags={"Tipos de Documento"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Lista completa de tipos de documento",
     *         @OA\JsonContent(
     *             @OA\Property(property="tipodocumento", type="array", @OA\Items(type="object")),
     *             @OA\Property(property="status", type="integer", example=200)
     *         )
     *     )
     * )
     */

    public function index()
    {
        $tipodocumento = Tipodocumento::all();

        $data = [
            "tipodocumento" => $tipodocumento,
            "status" => 200
        ];
        return response()->json($data, 200);
        //return "Obteniendo lista de epss del contepsador";

    }

    /**
     * @OA\Post(
     *     path="/api/tipodocumento",
     *     summary="Intento de creación de tipo de documento (no permitido)",
     *     tags={"Tipos de Documento"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=400,
     *         description="Este módulo no permite crear registros"
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
     *     path="/api/tipodocumento/{id}",
     *     summary="Obtener un tipo de documento por ID",
     *     tags={"Tipos de Documento"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID del tipo de documento",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Tipo de documento encontrado",
     *         @OA\JsonContent(
     *             @OA\Property(property="tipodocumento", type="object"),
     *             @OA\Property(property="status", type="integer", example=200)
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Tipo de documento no encontrado"
     *     )
     * )
     */

    public function show($id)
    {
        $tipodocumento = Tipodocumento::find($id);
        if (!$tipodocumento) {
            $data = [
                "mensage" => " No se encontro el tipo de contrato",
                "status" => 201
            ];
            return response()->json([$data], 201);
        }
        $data = [
            "tipodocumento" => $tipodocumento,
            "status" => 200
        ];
        return response()->json([$data], 200);
    }

    /**
     * @OA\Delete(
     *     path="/api/tipodocumento/{id}",
     *     summary="Intento de eliminación de tipo de documento (no permitido)",
     *     tags={"Tipos de Documento"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID del tipo de documento",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Este módulo no permite eliminar registros"
     *     )
     * )
     */

    public function destroy($id)
    {
        $data = [
            "mesaje " => "este modulo no permite eliminar, solo el administrador de base de datos lo puede hacer",
            "status" => 400
        ];
        return response()->json([$data], 400);
    }

    /**
     * @OA\Put(
     *     path="/api/tipodocumento/{id}",
     *     summary="Intento de actualización de tipo de documento (no permitido)",
     *     tags={"Tipos de Documento"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID del tipo de documento",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Este módulo no permite actualizar registros"
     *     )
     * )
     */

    public function update(Request $request, $id)
    {
        $data = [
            "mesaje " => "este modulo no permite Actualizar, solo el administrador de base de datos lo puede hacer",
            "status" => 400
        ];
        return response()->json([$data], 400);
    }

    /**
     * @OA\Patch(
     *     path="/api/tipodocumento/{id}",
     *     summary="Intento de actualización parcial de tipo de documento (no permitido)",
     *     tags={"Tipos de Documento"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID del tipo de documento",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Este módulo no permite actualizar registros"
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
}
