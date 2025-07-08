<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Tipohoras;
use Illuminate\Support\Facades\Validator;

/**
 * @OA\Tag(
 *     name="Tipos de Horas",
 *     description="Gestión de Tipos de Horas Extra"
 * )
 */


class tipohorasController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/tipohoras",
     *     summary="Listar todos los tipos de horas extra",
     *     tags={"Tipos de Horas"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Lista completa de tipos de horas",
     *         @OA\JsonContent(
     *             @OA\Property(property="tipohoras", type="array", @OA\Items(type="object")),
     *             @OA\Property(property="status", type="integer", example=200)
     *         )
     *     )
     * )
     */

    public function index()
    {
        $tipohoras = Tipohoras::all();

        $data = [
            "tipohoras" => $tipohoras,
            "status" => 200
        ];
        return response()->json($data, 200);
        //return "Obteniendo lista de epss del contepsador";

    }

    /**
     * @OA\Get(
     *     path="/api/tipohoras/create",
     *     summary="Vista para crear tipo de hora (no implementado)",
     *     tags={"Tipos de Horas"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(response=200, description="Sin implementación")
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
     * @OA\Post(
     *     path="/api/tipohoras",
     *     summary="Intento de creación de tipo de hora (no permitido)",
     *     tags={"Tipos de Horas"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(response=400, description="Este módulo no permite crear registros")
     * )
     */

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
     *     path="/api/tipohoras/{id}",
     *     summary="Obtener tipo de hora por ID",
     *     tags={"Tipos de Horas"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID del tipo de hora",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Tipo de hora encontrado",
     *         @OA\JsonContent(
     *             @OA\Property(property="Tipohoras", type="object"),
     *             @OA\Property(property="status", type="integer", example=200)
     *         )
     *     ),
     *     @OA\Response(response=201, description="Tipo de hora no encontrado")
     * )
     */


    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $tipohoras = Tipohoras::find($id);
        if (!$tipohoras) {
            $data = [
                "mensage" => " No se encontro tipoHora",
                "status" => 201
            ];
            return response()->json([$data], 201);
        }
        $data = [
            "Tipohoras" => $tipohoras,
            "status" => 200
        ];
        return response()->json([$data], 200);
    }

    /**
     * @OA\Get(
     *     path="/api/tipohoras/{id}/edit",
     *     summary="Vista para editar tipo de hora (no implementado)",
     *     tags={"Tipos de Horas"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID del tipo de hora",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(response=200, description="Sin implementación")
     * )
     */


    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {

    }

    /**
     * @OA\Put(
     *     path="/api/tipohoras/{id}",
     *     summary="Intento de actualización (no permitido)",
     *     tags={"Tipos de Horas"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID del tipo de hora",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(response=400, description="Este módulo no permite actualizar")
     * )
     */


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
     *     path="/api/tipohoras/{id}",
     *     summary="Intento de actualización parcial (no permitido)",
     *     tags={"Tipos de Horas"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID del tipo de hora",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(response=400, description="Este módulo no permite actualizar")
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
     *     path="/api/tipohoras/{id}",
     *     summary="Intento de eliminar tipo de hora (no permitido)",
     *     tags={"Tipos de Horas"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID del tipo de hora",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(response=400, description="Este módulo no permite eliminar registros")
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
