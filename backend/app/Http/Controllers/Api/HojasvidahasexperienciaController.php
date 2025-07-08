<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Hojasvida;
use App\Models\Hojasvidahasexperiencia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

/**
 * @OA\Tag(
 *     name="Experiencias en Hojas de Vida",
 *     description="Operaciones relacionadas con la experiencia laboral del usuario"
 * )
 */
class HojasvidahasexperienciaController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/hojasvidahasexperiencia",
     *     summary="Listar todas las experiencias en hojas de vida",
     *     tags={"Experiencias en Hojas de Vida"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Listado de relaciones experiencia-hoja de vida"
     *     )
     * )
     */

    public function index()
    {
        $experiencias = Hojasvidahasexperiencia::all();
        return response()->json([
            "data" => $experiencias,
            "status" => 200
        ]);
    }
    /**
     * @OA\Post(
     *     path="/api/hojasvidahasexperiencia",
     *     summary="Asociar experiencia a una hoja de vida",
     *     tags={"Experiencias en Hojas de Vida"},
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"idHojaDevida", "idExperiencia", "estado"},
     *             @OA\Property(property="idHojaDevida", type="integer", example=1),
     *             @OA\Property(property="idExperiencia", type="integer", example=5),
     *             @OA\Property(property="estado", type="boolean", example=true),
     *             @OA\Property(property="archivo", type="string", nullable=true, example="ruta/archivo.pdf")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Relación creada correctamente"
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
            'idHojaDevida' => 'required|integer|exists:hojasvida,idHojaDeVida',
            'idExperiencia' => 'required|integer|exists:experiencialaboral,idExperiencia',
            'estado' => 'required|boolean',
            'archivo' => 'nullable|string|max:255' // Se permite nulo
        ]);

        if ($validator->fails()) {
            return response()->json([
                'mensaje' => 'Error en la validación de la experiencia',
                'errors' => $validator->errors(),
                'status' => 400
            ], 400);
        }

        try {
            // Si no viene el campo 'archivo', se define como null
            $data = $request->all();
            $data['archivo'] = $data['archivo'] ?? null;

            $relacion = Hojasvidahasexperiencia::create($data);

            return response()->json([
                'mensaje' => 'Relación experiencia creada correctamente',
                'data' => $relacion,
                'status' => 201
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'mensaje' => 'Error al guardar la relación experiencia',
                'error' => $e->getMessage(),
                'status' => 500
            ], 500);
        }
    }
    /**
     * @OA\Get(
     *     path="/api/hojasvidahasexperiencia/{id}",
     *     summary="Obtener detalle de experiencia en hoja de vida por ID",
     *     tags={"Experiencias en Hojas de Vida"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID de la relación experiencia-hoja de vida",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Detalle encontrado"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="No encontrado"
     *     )
     * )
     */


    public function show($id)
    {
        $exp = Hojasvidahasexperiencia::find($id);
        if (!$exp) {
            return response()->json([
                "mensaje" => "Experiencia no encontrada",
                "status" => 404
            ], 404);
        }

        return response()->json([
            "data" => $exp,
            "status" => 200
        ]);
    }
    /**
     * @OA\Put(
     *     path="/api/hojasvidahasexperiencia/{id}",
     *     summary="Actualizar una experiencia laboral en hoja de vida",
     *     tags={"Experiencias en Hojas de Vida"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID de la experiencia",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"cargoEmpresa", "empresa", "tiempoExperiencia", "estado"},
     *             @OA\Property(property="cargoEmpresa", type="string", example="Desarrollador"),
     *             @OA\Property(property="empresa", type="string", example="Empresa XYZ"),
     *             @OA\Property(property="tiempoExperiencia", type="string", example="1 año"),
     *             @OA\Property(property="estado", type="boolean", example=true)
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Experiencia actualizada correctamente"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="No se encontró la experiencia"
     *     )
     * )
     */

    public function update(Request $request, $id)
    {
        $exp = Hojasvidahasexperiencia::find($id);
        if (!$exp) {
            return response()->json([
                "mensaje" => "Experiencia no encontrada",
                "status" => 404
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'cargoEmpresa' => 'required|string|max:100',
            'empresa' => 'required|string|max:100',
            'tiempoExperiencia' => 'required|string|max:45',
            'estado' => 'required|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                "errors" => $validator->errors(),
                "status" => 400
            ], 400);
        }

        $exp->update($request->only(['cargoEmpresa', 'empresa', 'tiempoExperiencia', 'estado']));

        return response()->json([
            "mensaje" => "Experiencia actualizada",
            "data" => $exp,
            "status" => 200
        ]);
    }
    /**
     * @OA\Patch(
     *     path="/api/hojasvidahasexperiencia/{id}",
     *     summary="Actualizar parcialmente una experiencia laboral",
     *     tags={"Experiencias en Hojas de Vida"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID de la experiencia",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=false,
     *         @OA\JsonContent(
     *             @OA\Property(property="cargoEmpresa", type="string", example="Auxiliar"),
     *             @OA\Property(property="empresa", type="string", example="Empresa ABC"),
     *             @OA\Property(property="tiempoExperiencia", type="string", example="2 años"),
     *             @OA\Property(property="estado", type="boolean", example=true)
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Actualización parcial exitosa"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Experiencia no encontrada"
     *     )
     * )
     */

    public function updatePartial(Request $request, $id)
    {
        $exp = Hojasvidahasexperiencia::find($id);
        if (!$exp) {
            return response()->json([
                "mensaje" => "Experiencia no encontrada",
                "status" => 404
            ], 404);
        }

        $exp->fill($request->only([
            'cargoEmpresa',
            'empresa',
            'tiempoExperiencia',
            'estado'
        ]));

        $exp->save();

        return response()->json([
            "mensaje" => "Experiencia actualizada parcialmente",
            "data" => $exp,
            "status" => 200
        ]);
    }
    /**
     * @OA\Delete(
     *     path="/api/hojasvidahasexperiencia/{id}",
     *     summary="Eliminar relación experiencia-hoja de vida",
     *     tags={"Experiencias en Hojas de Vida"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID de la relación",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Eliminado correctamente"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="No encontrado"
     *     )
     * )
     */

    public function destroy($id)
    {
        $relacion = Hojasvidahasexperiencia::find($id);
        if (!$relacion) {
            return response()->json([
                'mensaje' => 'Relación no encontrada',
                'status' => 404
            ]);
        }

        $relacion->delete();

        return response()->json([
            'mensaje' => 'Relación experiencia eliminada correctamente',
            'status' => 200
        ]);
    }

    /**
     * @OA\Get(
     *     path="/api/hojasvidahasexperiencia/documento/{numDocumento}",
     *     summary="Buscar experiencias laborales por número de documento",
     *     tags={"Experiencias en Hojas de Vida"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="numDocumento",
     *         in="path",
     *         required=true,
     *         description="Número de documento del usuario",
     *         @OA\Schema(type="integer", example=1234567890)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Experiencias encontradas"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Hoja de vida no encontrada"
     *     )
     * )
     */

    public function buscarPorDocumento($numDocumento)
    {
        // Buscar la hoja de vida por número de documento
        $hoja = Hojasvida::with('usuario')->where('usuarioNumDocumento', $numDocumento)->first();

        if (!$hoja) {
            return response()->json([
                "mensaje" => "Hoja de vida no encontrada",
                "status" => 404
            ], 404);
        }

        // Buscar experiencias relacionadas a la hoja de vida, incluyendo los detalles de la experiencia
        $experiencias = Hojasvidahasexperiencia::with('experiencia')
            ->where('idHojaDevida', $hoja->idHojaDeVida)
            ->get();

        return response()->json([
            "data" => [
                "hojaDeVida" => $hoja,
                "usuario" => $hoja->usuario, // Relación directa desde hoja de vida
                "experiencias" => $experiencias
            ],
            "status" => 200
        ]);
    }



    public function buscarPorHojaDeVida($idHojaDevida)
    {
        $experiencias = Hojasvidahasexperiencia::where('idHojaDevida', $idHojaDevida)->get();

        return response()->json([
            "data" => $experiencias,
            "status" => 200
        ]);
    }
    /**
     * @OA\Get(
     *     path="/api/hojasvidahasexperiencia/descargar/{id}",
     *     summary="Descargar archivo adjunto de experiencia laboral",
     *     tags={"Experiencias en Hojas de Vida"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID del archivo de experiencia",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Archivo descargado con éxito",
     *         @OA\MediaType(mediaType="application/octet-stream")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Archivo no encontrado"
     *     )
     * )
     */

    public function descargarArchivo($id)
    {
        $registro = Hojasvidahasexperiencia::find($id);

        if (!$registro || !$registro->archivo) {
            return response()->json([
                "mensaje" => "Archivo no encontrado",
                "status" => 404
            ], 404);
        }

        $ruta = storage_path('app/public/' . str_replace('storage/', '', $registro->archivo));

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
     *     path="/api/hojasvidahasexperiencia/hoja/{idHojaDeVida}",
     *     summary="Buscar experiencias por hoja de vida",
     *     tags={"Experiencias en Hojas de Vida"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="idHojaDeVida",
     *         in="path",
     *         required=true,
     *         description="ID de la hoja de vida",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Listado de experiencias encontradas"
     *     )
     * )
     */
    public function buscarPorHojaId($idHojaDeVida)
    {
        try {
            $relaciones = Hojasvidahasexperiencia::with('experiencia')
                ->where('idHojaDevida', $idHojaDeVida)
                ->get();

            return response()->json([
                'data' => $relaciones,
                'status' => 200
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'mensaje' => 'Error al obtener experiencias de la hoja de vida',
                'error' => $e->getMessage(),
                'status' => 500
            ], 500);
        }
    }
}
