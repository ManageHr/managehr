<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Incapacidad;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use OpenApi\Annotations as OA;

class incapacidadController extends Controller
{

    /**
     * @OA\Get(
     *     path="/api/incapacidades",
     *     summary="Listar todas las incapacidades",
     *     description="Obtiene un listado de todas las incapacidades registradas con sus relaciones.",
     *     tags={"Incapacidades"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Listado obtenido con éxito"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Error del servidor"
     *     )
     * )
     */



    public function index(Request $request)
    {
        try {
            $incapacidades = Incapacidad::with([
                'contrato.hojaDeVida.usuario.user',
                'contrato.area'  
            ])->get();

            return response()->json([
                'data' => $incapacidades
            ], 200);
        } catch (\Exception $e) {
            Log::error('Error al obtener incapacidades (IncapacidadController::index): ' . $e->getMessage());

            return response()->json([
                'message' => 'Ocurrió un error al obtener las incapacidades.',
                'error' => $e->getMessage()
            ], 500);
        }
    }


    /**
     * @OA\Post(
     *     path="/api/incapacidades",
     *     summary="Registrar nueva incapacidad",
     *     description="Crea una nueva incapacidad con descripción, fechas y archivo (opcional).",
     *     tags={"Incapacidades"},
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 required={"descrip", "fechaInicio", "fechaFinal", "contratoId"},
     *                 @OA\Property(property="descrip", type="string", example="Incapacidad médica general"),
     *                 @OA\Property(property="archivo", type="string", format="binary", nullable=true),
     *                 @OA\Property(property="fechaInicio", type="string", format="date", example="2025-07-06"),
     *                 @OA\Property(property="fechaFinal", type="string", format="date", example="2025-07-10"),
     *                 @OA\Property(property="contratoId", type="integer", example=12)
     *             )
     *         )
     *     ),
     *     @OA\Response(response=201, description="Incapacidad creada correctamente"),
     *     @OA\Response(response=400, description="Error de validación"),
     *     @OA\Response(response=500, description="Error interno del servidor")
     * )
     */


    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'descrip' => 'required|string|max:500',
            'archivo' => 'nullable|file|max:5120',
            'fechaInicio' => 'required|date',
            'fechaFinal' => 'required|date',
            'contratoId' => 'required|integer'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'mensaje' => 'Error en la validación de datos de la incapacidad',
                'errors' => $validator->errors(),
                'status' => 400
            ], 400);
        }
        if ($request->hasFile('archivo')) {
            $file = $request->file('archivo');
            $folder = 'Archivos/' . $request->input('contratoId');

            $extension = $file->getClientOriginalExtension();
            $filename = $request->input('contratoId') . '.' . $extension;
            $path = $file->storeAs($folder, $filename, 'public');

            $validated['archivo'] = 'storage/' . $path;
        }
        try {
            $incapacidad = Incapacidad::create([
                'descrip' => $request->descrip,
                'archivo' => $request->archivo,
                'fechaInicio' => $request->fechaInicio,
                'fechaFinal' => $request->fechaFinal,
                'contratoId' => $request->contratoId

            ]);

            return response()->json([
                'mensaje' => 'incapacidad creado correctamente',
                'incapacidad' => $incapacidad,
                'status' => 201
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'mensaje' => 'Error al crear el incapacidad',
                'error' => $e->getMessage(),
                'status' => 500
            ], 500);
        }
    }


    /**
     * @OA\Get(
     *     path="/api/incapacidades/{id}",
     *     summary="Consultar incapacidad por ID",
     *     description="Devuelve los datos de una incapacidad específica.",
     *     tags={"Incapacidades"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID de la incapacidad",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(response=200, description="Incapacidad encontrada"),
     *     @OA\Response(response=404, description="Incapacidad no encontrada")
     * )
     */

   
    public function show($id)
    {
        $incapacidad = Incapacidad::find($id);
        $data = [
            "incapacidad" => $incapacidad,
            "status" => 200
        ];
        return response()->json($data, 200);
    }

     

    /**
     * @OA\Delete(
     *     path="/api/incapacidades/{id}",
     *     summary="Eliminar incapacidad",
     *     description="Elimina una incapacidad existente por su ID.",
     *     tags={"Incapacidades"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID de la incapacidad a eliminar",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(response=200, description="Incapacidad eliminada con éxito"),
     *     @OA\Response(response=404, description="Incapacidad no encontrada")
     * )
     */

    public function destroy($id)
    {
        $incapacidad = Incapacidad::find($id);
        if (!$incapacidad) {
            $data = [
                "mensage" => " No se encontro Incapacidad",
                "status" => 404
            ];
            return response()->json([$data], 404);
        }
        $incapacidad->delete();
        $data = [
            "incapacidad:" => 'incapacidad eliminado',
            "status" => 200
        ];
        return response()->json([$data], 200);
    }


    /**
     * @OA\Put(
     *     path="/api/incapacidades/{id}",
     *     summary="Actualizar incapacidad (total)",
     *     description="Modifica todos los campos de una incapacidad existente.",
     *     tags={"Incapacidades"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID de la incapacidad",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 required={"descrip", "fechaInicio", "fechaFinal", "contratoId"},
     *                 @OA\Property(property="descrip", type="string", example="Incapacidad por cirugía"),
     *                 @OA\Property(property="archivo", type="string", format="binary", nullable=true),
     *                 @OA\Property(property="fechaInicio", type="string", format="date", example="2025-07-01"),
     *                 @OA\Property(property="fechaFinal", type="string", format="date", example="2025-07-05"),
     *                 @OA\Property(property="contratoId", type="integer", example=7)
     *             )
     *         )
     *     ),
     *     @OA\Response(response=200, description="Incapacidad actualizada con éxito"),
     *     @OA\Response(response=400, description="Error de validación"),
     *     @OA\Response(response=404, description="Incapacidad no encontrada")
     * )
     */

    public function update(Request $request, $id)
    {
        $incapacidad = Incapacidad::find($id);
        if (!$incapacidad) {
            $data = [
                "mensage" => " No se encontro incapacidad",
                "status" => 404
            ];
            return response()->json([$data], 404);
        }
        $validator = Validator::make($request->all(), [

            'descrip' => 'required|string|max:500',
            'archivo' => 'nullable|file|max:5120',
            'fechaInicio' => 'required|date',
            'fechaFinal' => 'required|date',
            'contratoId' => 'required|integer'
        ]);
        if ($validator->fails()) {
            $data = [
                "errors" => $validator->errors(),
                "status" => 400
            ];
            return response()->json([$data], 400);
        }
        if ($request->hasFile('archivo')) {
            $file = $request->file('archivo');
            $folder = 'Archivos/' . $request->input('numDocumento');

            $extension = $file->getClientOriginalExtension();
            $filename = $request->input('numDocumento') . '.' . $extension;
            $path = $file->storeAs($folder, $filename, 'public');

            $validated['archivo'] = 'storage/' . $path;
        }

        $incapacidad->descrip = $request->descrip;
        $incapacidad->archivo = $request->archivo;
        $incapacidad->fechaInicio = $request->fechaInicio;
        $incapacidad->fechaFinal = $request->fechaFinal;
        $incapacidad->contratoId = $request->contratoId;

        try {
            $incapacidad->save();
            $data = [
                "incapacidad" => $incapacidad,
                "status" => 200
            ];
            return response()->json([$data], 200);
        } catch (\Exception $e) {
            return response()->json([
                "mensaje" => "Error al modificar la incapacidad",
                "error" => $e->getMessage(),
                "status" => 500
            ], 500);
        }
    }

 /**
     * @OA\Patch(
     *     path="/api/incapacidades/{id}",
     *     summary="Actualizar incapacidad (parcial)",
     *     description="Actualiza uno o varios campos de la incapacidad indicada.",
     *     tags={"Incapacidades"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID de la incapacidad",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=false,
     *         @OA\JsonContent(
     *             @OA\Property(property="descrip", type="string", example="Cambio de diagnóstico"),
     *             @OA\Property(property="archivo", type="string", example="storage/Archivos/archivo.pdf"),
     *             @OA\Property(property="fechaInicio", type="string", format="date", example="2025-07-01"),
     *             @OA\Property(property="fechaFinal", type="string", format="date", example="2025-07-03"),
     *             @OA\Property(property="contratoId", type="integer", example=15)
     *         )
     *     ),
     *     @OA\Response(response=200, description="Actualización parcial exitosa"),
     *     @OA\Response(response=400, description="Error de validación"),
     *     @OA\Response(response=404, description="Incapacidad no encontrada")
     * )
     */

    public function updatePartial(Request $request, $id)
    {
        $incapacidad = Incapacidad::find($id);
        if (!$incapacidad) {
            $data = [
                "mensage" => " No se encontro incapacidad",
                "status" => 404
            ];
            return response()->json([$data], 404);
        }
        $validator = Validator::make($request->all(), [

            'descrip' => 'string|max:500',
            'archivo' => 'string|max:50',
            'fechaInicio' => 'date',
            'fechaFinal' => 'date',
            'contratoId' => 'integer',
        ]);
        if ($validator->fails()) {
            $data = [
                "mesaje " => "Error al validar incapacidad",
                "errors" => $validator->errors(),
                "status" => 400
            ];
            return response()->json([$data], 400);
        }
        if ($request->has("descrip")) {
            $incapacidad->descrip = $request->descrip;
        }
        if ($request->has("archivo")) {
            $incapacidad->archivo = $request->archivo;
        }
        if ($request->has("fechaInicio")) {
            $incapacidad->fechaInicio = $request->fechaInicio;
        }
        if ($request->has("fechaFinal")) {
            $incapacidad->fechaFinal = $request->fechaFinal;
        }
        if ($request->has("contratoId")) {
            $incapacidad->contratoId = $request->contratoId;
        }


        $incapacidad->save();
        $data = [
            "incapacidad:" => $incapacidad,
            "status" => 200
        ];
        return response()->json([$data], 200);
    }
    /**
     * @OA\Patch(
     *     path="/api/incapacidad/estado/{id}",
     *     summary="Actualizar el estado de una incapacidad",
     *     description="Actualiza el estado (0,1,2) de una incapacidad existente por su ID.",
     *     tags={"Incapacidades"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID de la incapacidad",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"estado"},
     *             @OA\Property(property="estado", type="integer", enum={0, 1}, example=1)
     *         )
     *     ),
     *     @OA\Response(response=200, description="Estado actualizado correctamente"),
     *     @OA\Response(response=404, description="Incapacidad no encontrada"),
     *     @OA\Response(response=400, description="Error en la validación")
     * )
     */

    public function cambiarEstado(Request $request, $id)
    {
        $incapacidad = Incapacidad::find($id);

        if (!$incapacidad) {
            return response()->json([
                'mensaje' => 'Incapacidad no encontrada',
                'status' => 404
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'estado' => 'required|integer|in:0,1,2'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'mensaje' => 'Error en la validación',
                'errors' => $validator->errors(),
                'status' => 400
            ], 400);
        }

        $incapacidad->estado = $request->estado;
        $incapacidad->save();

        return response()->json([
            'mensaje' => 'Estado de la incapacidad actualizado correctamente',
            'incapacidad' => $incapacidad,
            'status' => 200
        ]);
    }
}
