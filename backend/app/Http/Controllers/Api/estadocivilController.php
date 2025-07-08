<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Estadocivil;
use Illuminate\Support\Facades\Validator;

/**
 * @OA\Tag(
 *     name="Estado Civil",
 *     description="Gestión del estado civil (solo lectura pública) ya que se cargan todos los estados civiles. "
 * )
 */
class estadocivilController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/estadocivil",
     *     summary="Listar todos los estados civiles",
     *     tags={"Estado Civil"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Listado de estados civiles obtenido correctamente",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="integer", example=200),
     *             @OA\Property(property="estadocivil", type="array", 
     *                 @OA\Items(
     *                     @OA\Property(property="idEstadocivil", type="integer", example=1),
     *                     @OA\Property(property="nombreEstado", type="string", example="Soltero")
     *                 )
     *             )
     *         )
     *     )
     * )
     */
    public function index()
    {
        $estadocivil = Estadocivil::orderBy('nombreEstado', 'asc')->get();;

        $data = [
            "estadocivil" => $estadocivil,
            "status" => 200
        ];
        return response()->json($data, 200);
        //return "Obteniendo lista de epss del contepsador";

    }

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
     *     path="/api/estadocivil/{id}",
     *     summary="Obtener un estado civil por ID",
     *     security={{"bearerAuth":{}}},
     *     tags={"Estado Civil"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID del estado civil",
     *         required=true,
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Estado civil encontrado",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="integer", example=200),
     *             @OA\Property(property="estadocivil", type="object",
     *                 @OA\Property(property="idEstadocivil", type="integer", example=1),
     *                 @OA\Property(property="nombreEstado", type="string", example="Casado")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Estado civil no encontrado"
     *     )
     * )
     */
    public function show(string $id)
    {
        $estadocivil = Estadocivil::where('idEstadocivil', $id)->first();
        if (!$estadocivil) {
            $data = [
                "mensage" => " No se encontro eps",
                "status" => 201
            ];
            return response()->json([$data], 201);
        }
        $data = [
            "estadocivil" => $estadocivil,
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
    public function updatePartial(Request $request, $id)
    {
        $data = [
            "mesaje " => "este modulo no permite actualizar, solo el administrador de base de datos lo puede hacer",
            "status" => 400
        ];
        return response()->json([$data], 400);
    }

    
    public function destroy(string $id)
    {
        $data = [
            "mesaje " => "este modulo no permite Actualizar, solo el administrador de base de datos lo puede hacer",
            "status" => 400
        ];
        return response()->json([$data], 400);
    }
}
