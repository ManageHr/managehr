<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Hojasvidahasestudios;
use App\Models\Hojasvida;
use Illuminate\Support\Facades\Validator;

/**
 * @OA\Tag(
 *     name="Hojas de Vida",
 *     description="Gestión de hojas de vida de los empleados y personal externo loguados"
 * )
 */
class hojasvidaController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/hojasvida",
     *     tags={"Hojas de Vida"},
     *     summary="Listar todas las hojas de vida",
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Lista completa de hojas de vida",
     *         @OA\JsonContent(
     *             @OA\Property(property="data", type="array", @OA\Items(type="object")),
     *             @OA\Property(property="status", type="integer", example=200)
     *         )
     *     )
     * )
     */

    public function index()
    {
        $Hojasvidas = Hojasvida::all();
        return response()->json([
            "data" => $Hojasvidas,
            "status" => 200
        ], 200);
    }
    /**
     * @OA\Post(
     *     path="/api/hojasvida",
     *     tags={"Hojas de Vida"},
     *     summary="Crear una hoja de vida",
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"claseLibretaMilitar", "numeroLibretaMilitar", "usuarioNumDocumento"},
     *             @OA\Property(property="claseLibretaMilitar", type="string", maxLength=45),
     *             @OA\Property(property="numeroLibretaMilitar", type="string", maxLength=45),
     *             @OA\Property(property="usuarioNumDocumento", type="integer")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Hoja de vida creada correctamente"
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Error de validación"
     *     )
     * )
     */

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "claseLibretaMilitar" => "required|max:45",
            "numeroLibretaMilitar" => "required|max:45",
            "usuarioNumDocumento" => "required"
        ]);

        if ($validator->fails()) {
            $data = [
                "mensaje" => "Error en la validación de Hojas de vida",
                "errors" => $validator->errors(),
                "status" => 400
            ];
            return response()->json($data, 400); // ✅ sin los corchetes extra
        }

        try {
            // dd($request->all());


            $Hojasvida = Hojasvida::create([
                "claseLibretaMilitar" => $request->claseLibretaMilitar,
                "numeroLibretaMilitar" => $request->numeroLibretaMilitar,
                "usuarioNumDocumento" => $request->usuarioNumDocumento
            ]);


            return response()->json([
                "mensaje" => "Hoja de vida creada correctamente",
                "Hojasvida" => $Hojasvida,
                "status" => 201
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                "mensaje" => "Error al crear la HV",
                "error" => $e->getMessage(),
                "status" => 500
            ], 500);
        }
    }
    /**
     * @OA\Get(
     *     path="/api/hojasvida/{id}",
     *     tags={"Hojas de Vida"},
     *     summary="Obtener una hoja de vida por ID",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID de la hoja de vida",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Hoja de vida encontrada"
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Hoja de vida no encontrada"
     *     )
     * )
     */


    public function show($id)
    {
        $Hojasvida = Hojasvida::with([
            'usuario.tipoDocumento',
            'usuario.genero',
            'usuario.estadoCivil',
            'usuario.eps',
            'usuario.pensiones',
            'usuario.nacionalidad'
        ])->where('idHojasDeVida', $id)->first();

        if (!$Hojasvida) {
            $data = [
                "mensage" => " No se encontro Hojasvida",
                "status" => 201
            ];
            return response()->json([$data], 201);
        }
        $data = [
            "rol" => $Hojasvida,
            "status" => 200
        ];
        return response()->json([$data], 200);
    }
    /**
     * @OA\Delete(
     *     path="/api/hojasvida/{id}",
     *     tags={"Hojas de Vida"},
     *     summary="Eliminar una hoja de vida por ID",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID de la hoja de vida",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Hoja de vida eliminada"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Hoja de vida no encontrada"
     *     )
     * )
     */

    public function destroy($id)
    {
        $Hojasvida = Hojasvida::find($id);
        if (!$Hojasvida) {
            $data = [
                "mensage" => " No se encontro Hoja de vida",
                "status" => 404
            ];
            return response()->json([$data], 404);
        }
        $Hojasvida->delete();
        $data = [
            "rol" => 'Hoja de vida eliminada',
            "status" => 200
        ];
        return response()->json([$data], 200);
    }
    /**
     * @OA\Put(
     *     path="/api/hojasvida/{id}",
     *     tags={"Hojas de Vida"},
     *     summary="Actualizar completamente una hoja de vida",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID de la hoja de vida",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"claseLibretaMilitar", "numeroLibretaMilitar", "usuarioNumDocumento"},
     *             @OA\Property(property="claseLibretaMilitar", type="string"),
     *             @OA\Property(property="numeroLibretaMilitar", type="string"),
     *             @OA\Property(property="usuarioNumDocumento", type="integer")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Hoja de vida actualizada correctamente"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Hoja de vida no encontrada"
     *     )
     * )
     */

    public function update(Request $request, $id)
    {
        $Hojasvida = Hojasvida::find($id);
        if (!$Hojasvida) {
            $data = [
                "mensage" => " No se encontro Hoja de vida",
                "status" => 404
            ];
            return response()->json([$data], 404);
        }
        $validator = Validator::make($request->all(), [
            "claseLibretaMilitar" => "required|max:45",
            "numeroLibretaMilitar" => "required|max:45",
            "usuarioNumDocumento" => "required"
        ]);
        if ($validator->fails()) {
            $data = [
                "errors" => $validator->errors(),
                "status" => 400
            ];
            return response()->json([$data], 400);
        }
        $Hojasvida->claseLibretaMilitar = $request->claseLibretaMilitar;
        $Hojasvida->numeroLibretaMilitar = $request->numeroLibretaMilitar;
        $Hojasvida->usuarioNumDocumento = $request->usuarioNumDocumento;
        try {
            $Hojasvida->save();
            $data = [
                "hojasvida" => $Hojasvida,
                "status" => 200
            ];
            return response()->json([$data], 200);
        } catch (\Exception $e) {
            return response()->json([
                "mensaje" => "Error al modificar la hoja de vida",
                "error" => $e->getMessage(),
                "status" => 500
            ], 500);
        }
    }
    /**
     * @OA\Patch(
     *     path="/api/hojasvida/{id}",
     *     tags={"Hojas de Vida"},
     *     summary="Actualizar parcialmente una hoja de vida",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID de la hoja de vida",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         @OA\JsonContent(
     *             @OA\Property(property="claseLibretaMilitar", type="string"),
     *             @OA\Property(property="numeroLibretaMilitar", type="string"),
     *             @OA\Property(property="usuarioNumDocumento", type="integer")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Hoja de vida actualizada parcialmente"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Hoja de vida no encontrada"
     *     )
     * )
     */

    public function updatePartial(Request $request, $id)
    {
        $Hojasvida = Hojasvida::find($id);
        if (!$Hojasvida) {
            $data = [
                "mensage" => " No se encontro rol",
                "status" => 404
            ];
            return response()->json([$data], 404);
        }
        $validator = Validator::make($request->all(), [
            "claseLibretaMilitar" => "max:45",
            "numeroLibretaMilitar" => "max:45",
            "usuarioNumDocumento" => "integer"
        ]);
        if ($validator->fails()) {
            $data = [
                "mesaje " => "Error al validar Hoja de vida",
                "errors" => $validator->errors(),
                "status" => 400
            ];
            return response()->json([$data], 400);
        }
        if ($request->has("idHojaDeVida")) {
            $Hojasvida->idHojasvida = $request->idHojasvida;
        }
        if ($request->has("claseLibretaMilitar")) {
            $Hojasvida->claseLibretaMilitar = $request->claseLibretaMilitar;
        }
        if ($request->has("numeroLibretaMilitar")) {
            $Hojasvida->numeroLibretaMilitar = $request->numeroLibretaMilitar;
        }
        if ($request->has("usuarioNumDocumento")) {
            $Hojasvida->usuarioNumDocumento = $request->usuarioNumDocumento;
        }
        $Hojasvida->save();
        $data = [
            "rol" => $Hojasvida,
            "status" => 200
        ];
        return response()->json([$data], 200);
    }
    /**
     * @OA\Get(
     *     path="/api/hojasvida/documento/{numDocumento}",
     *     tags={"Hojas de Vida"},
     *     summary="Buscar hoja de vida por número de documento",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="numDocumento",
     *         in="path",
     *         required=true,
     *         description="Número de documento del usuario",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Hoja de vida encontrada"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Hoja de vida no encontrada"
     *     )
     * )
     */

    public function buscarPorDocumento($numDocumento)
    {
        $hoja = Hojasvida::with([
            'usuario.tipoDocumento',
            'usuario.genero',
            'usuario.estadoCivil',
            'usuario.eps',
            'usuario.pensiones',
            'usuario.nacionalidad'
        ])->where('usuarioNumDocumento', $numDocumento)->first();


        if (!$hoja) {
            return response()->json([
                "mensaje" => "Hoja de vida no encontrada para el documento $numDocumento",
                "status" => 404
            ], 404);
        }

        return response()->json([
            "hojaDeVida" => $hoja,
            "status" => 200
        ], 200);
    }
    /**
     * @OA\Get(
     *     path="/api/hojasvida/archivo/{id}",
     *     tags={"Hojas de Vida"},
     *     summary="Descargar archivo de hoja de vida por ID",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID del archivo relacionado con la hoja de vida",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Archivo descargado"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Archivo no encontrado"
     *     )
     * )
     */

    public function descargarArchivo($id)
    {
        $registro = Hojasvidahasestudios::find($id);

        if (!$registro || !$registro->archivo) {
            return response()->json([
                "mensaje" => "Archivo no encontrado",
                "status" => 404
            ], 404);
        }

        $ruta = storage_path('app/public/' . $registro->archivo);

        if (!file_exists($ruta)) {
            return response()->json([
                "mensaje" => "El archivo no existe físicamente",
                "status" => 404
            ], 404);
        }

        return response()->download($ruta);
    }

    /**
     * @OA\Get(
     *     path="/api/hojasvida/obtener/{numDocumento}",
     *     tags={"Hojas de Vida"},
     *     summary="Obtener hoja de vida detallada por número de documento",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="numDocumento",
     *         in="path",
     *         required=true,
     *         description="Número de documento del usuario",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Hoja de vida encontrada"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Hoja de vida no encontrada"
     *     )
     * )
     */

    public function obtenerHojaDeVida($numDocumento)
    {
        try {
            $hoja = HojasVida::with(['usuario', 'experiencias', 'estudios'])
                ->where('documento', $numDocumento)
                ->first();

            if (!$hoja) {
                return response()->json([
                    'message' => 'No se encontró hoja de vida',
                    'status' => 404
                ], 404);
            }

            return response()->json([
                'hojaDeVida' => $hoja,
                'status' => 200
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al obtener hoja de vida',
                'error' => $e->getMessage(),
                'status' => 500
            ], 500);
        }
    }
}
