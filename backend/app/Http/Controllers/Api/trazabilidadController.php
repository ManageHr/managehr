<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Trazabilidad;
use Illuminate\Http\Request;

class trazabilidadController extends Controller
{
        /**
     * @OA\Get(
     *     path="/api/trazabilidad",
     *     summary="Listar todas las trazabilidades",
     *     description="Devuelve una lista completa de los registros de trazabilidad en el sistema.",
     *     tags={"Trazabilidad"},
     *     @OA\Response(
     *         response=200,
     *         description="Lista de trazabilidades obtenida correctamente",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="tipodocumento", type="array", @OA\Items(
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="descripcion", type="string", example="Contrato firmado"),
     *                 @OA\Property(property="fecha", type="string", format="date-time", example="2025-07-07T13:45:00Z")
     *             )),
     *             @OA\Property(property="status", type="integer", example=200)
     *         )
     *     )
     * )
     */

    public function index()
    {
        $tipodocumento = Trazabilidad::all();

        $data = [
            "tipodocumento" => $tipodocumento,
            "status" => 200
        ];
        return response()->json($data, 200);
        //return "Obteniendo lista de epss del contepsador";

    }

        /**
     * @OA\Post(
     *     path="/api/trazabilidad",
     *     summary="Crear trazabilidad (No permitido)",
     *     description="Este endpoint está deshabilitado. Solo el administrador de base de datos puede crear registros.",
     *     tags={"Trazabilidad"},
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
     *     path="/api/trazabilidad/{id}",
     *     summary="Obtener una trazabilidad por ID",
     *     description="Devuelve un registro de trazabilidad específico según su identificador único.",
     *     tags={"Trazabilidad"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID del registro de trazabilidad",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Trazabilidad encontrada",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="tipodocumento", type="object",
     *                 @OA\Property(property="id", type="integer", example=3),
     *                 @OA\Property(property="descripcion", type="string", example="Documento entregado al usuario"),
     *                 @OA\Property(property="fecha", type="string", format="date-time", example="2025-07-07T14:20:00Z")
     *             ),
     *             @OA\Property(property="status", type="integer", example=200)
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Trazabilidad no encontrada"
     *     )
     * )
     */

    public function show($id)
    {
        $tipodocumento = Trazabilidad::find($id);
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
     *     path="/api/trazabilidad/{id}",
     *     summary="Eliminar una trazabilidad por ID",
     *     description="Elimina un registro de trazabilidad específico por su ID. Solo accesible si el registro existe.",
     *     tags={"Trazabilidad"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID del registro de trazabilidad",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Trazabilidad eliminada exitosamente",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="permisos", type="string", example="trazabilidad eliminada"),
     *             @OA\Property(property="status", type="integer", example=200)
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Trazabilidad no encontrada"
     *     )
     * )
     */

    public function destroy($id)
    {
        $trazabilidad = Trazabilidad::find($id);
        if (!$trazabilidad) {
            $data = [
                "mensage" => " No se encontro trazabilidad",
                "status" => 404
            ];
            return response()->json([$data], 404);
        } else {
            $trazabilidad->delete();
            $data = [
                "permisos" => 'trazabilidad eliminada',
                "status" => 200
            ];
            return response()->json([$data], 200);
        }
    }
        /**
     * @OA\Put(
     *     path="/api/trazabilidad/{id}",
     *     summary="Actualizar trazabilidad (No permitido)",
     *     description="Este endpoint está deshabilitado. Solo el administrador de base de datos puede modificar registros.",
     *     tags={"Trazabilidad"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID del registro de trazabilidad",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Operación no permitida para el usuario"
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
     *     path="/api/trazabilidad/{id}",
     *     summary="Actualizar parcialmente una trazabilidad (No permitido)",
     *     description="Este endpoint está deshabilitado. Solo el administrador de base de datos puede actualizar registros.",
     *     tags={"Trazabilidad"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID del registro de trazabilidad",
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
}
