<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\TipoContrato;
use Illuminate\Http\Request;

/**
 * @OA\Tag(
 *     name="Tipos de Contrato",
 *     description="Gestión de Tipos de Contrato"
 * )
 *
 * @OA\SecurityScheme(
 *     securityScheme="bearerAuth",
 *     type="http",
 *     scheme="bearer",
 *     bearerFormat="JWT"
 * )
 */

class tipoContratoController extends Controller
{

    /**
     * @OA\Get(
     *     path="/api/tipoContrato",
     *     summary="Listar todos los tipos de contrato",
     *     tags={"Tipos de Contrato"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Lista completa de tipos de contrato",
     *         @OA\JsonContent(
     *             @OA\Property(property="tipocontrato", type="array", @OA\Items(type="object")),
     *             @OA\Property(property="status", type="integer", example=200)
     *         )
     *     )
     * )
     */

    public function index()
    {
        $tipocontrato = TipoContrato::all();

        $data = [
            "tipocontrato" => $tipocontrato,
            "status" => 200
        ];
        return response()->json($data, 200);
        //return "Obteniendo lista de epss del contepsador";

    }

    /**
     * @OA\Post(
     *     path="/api/tipoContrato",
     *     summary="Intento de creación de tipo de contrato (no permitido)",
     *     tags={"Tipos de Contrato"},
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
     *     path="/api/tipoContrato/{id}",
     *     summary="Obtener un tipo de contrato por ID",
     *     tags={"Tipos de Contrato"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID del tipo de contrato",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Tipo de contrato encontrado",
     *         @OA\JsonContent(
     *             @OA\Property(property="tipocontrato", type="object"),
     *             @OA\Property(property="status", type="integer", example=200)
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Tipo de contrato no encontrado"
     *     )
     * )
     */

    public function show($id)
    {
        $tipocontrato = TipoContrato::where('idTipoContrato', $id)->first();
        if (!$tipocontrato) {
            $data = [
                "mensage" => " No se encontro el tipo de contrato",
                "status" => 201
            ];
            return response()->json([$data], 201);
        }
        $data = [
            "tipocontrato" => $tipocontrato,
            "status" => 200
        ];
        return response()->json([$data], 200);
    }

    /**
     * @OA\Delete(
     *     path="/api/tipoContrato/{id}",
     *     summary="Intento de eliminación de tipo de contrato (no permitido)",
     *     tags={"Tipos de Contrato"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID del tipo de contrato",
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
     *     path="/api/tipoContrato/{id}",
     *     summary="Intento de actualización de tipo de contrato (no permitido)",
     *     tags={"Tipos de Contrato"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID del tipo de contrato",
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
     *     path="/api/tipoContrato/{id}",
     *     summary="Intento de actualización parcial de tipo de contrato (no permitido)",
     *     tags={"Tipos de Contrato"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID del tipo de contrato",
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
