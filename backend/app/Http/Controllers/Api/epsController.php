<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Eps;
use Illuminate\Support\Facades\Validator;

/**
 * @OA\Tag(
 *     name="EPS",
 *     description="Gestión de EPS este módulo permite la gestión de las EPS solo consulta ya que de base de datos \n no se puede modificar registros ni eliminar solo los administradores de base de datos pueden hacerlo."
 * )
 */

class epsController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/epss",
     *     summary="Listar todas las EPS",
     *     tags={"EPS"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Lista de EPS obtenida correctamente",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="integer", example=200),
     *             @OA\Property(
     *                 property="eps",
     *                 type="array",
     *                 @OA\Items(
     *                     @OA\Property(property="codigoEps", type="integer", example=1),
     *                     @OA\Property(property="nombreEps", type="string", example="Salud Total EPS")
     *                 )
     *             )
     *         )
     *     )
     * )
     */
    public function index()
    {
    

        $epss = Eps::orderBy('nombreEps', 'asc')->get();;

        $data = [
            "eps" => $epss,
            "status" => 200
        ];
        return response()->json($data, 200);
        //return "Obteniendo lista de epss del contepsador";

    }
    /**
     * @OA\Post(
     *     path="/api/epss",
     *     summary="Crear EPS (deshabilitado)",
     *     tags={"EPS"},
     *     @OA\Response(
     *         response=400,
     *         description="Este módulo no permite crear EPS. Solo el DB tiene acceso."
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
     *     path="/api/epss/{codigoEps}",
     *     summary="Obtener EPS por código de campo string",
     *     tags={"EPS"},
     *     security={{ "bearerAuth": {} }},
     *     @OA\Parameter(
     *         name="codigoEps",
     *         in="path",
     *         required=true,
     *         description="Código único de la EPS",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="EPS encontrada",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="integer", example=200),
     *             @OA\Property(property="eps", type="object",
     *                 @OA\Property(property="codigoEps", type="integer", example=1),
     *                 @OA\Property(property="nombreEps", type="string", example="Nueva EPS")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="EPS no encontrada"
     *     )
     * )
     */
    public function show($id)
    {
        $eps = Eps::where('codigoEps', $id)->first();
        if (!$eps) {
            $data = [
                "mensage" => " No se encontro eps",
                "status" => 201
            ];
            return response()->json([$data], 201);
        }
        $data = [
            "eps" => $eps,
            "status" => 200
        ];
        return response()->json([$data], 200);
    }
    public function destroy($id)
    {
        $data = [
            "mesaje " => "este modulo no permite eliminar, solo el administrador de base de datos lo puede hacer",
            "status" => 400
        ];
        return response()->json([$data], 400);
    }
    public function update(Request $request, $id)
    {
        $data = [
            "mesaje " => "este modulo no permite Actualizar, solo el administrador de base de datos lo puede hacer",
            "status" => 400
        ];
        return response()->json([$data], 400);
    }
    public function updatePartial(Request $request, $id)
    {
        $data = [
            "mesaje " => "este modulo no permite actualizar, solo el administrador de base de datos lo puede hacer",
            "status" => 400
        ];
        return response()->json([$data], 400);
    }
}
