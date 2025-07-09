<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Incapacidad;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

/**
 * @OA\Tag(
 *     name="Incapacidades",
 *     description="Gestión general de incapacidades. Permite a administradores y personal autorizado registrar, consultar, actualizar y eliminar incapacidades de cualquier empleado."
 * )
 */
class incapacidadController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/incapacidad",
     *     summary="Obtener todas las incapacidades",
     *     tags={"Incapacidades"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Lista de incapacidades obtenida exitosamente",
     *         @OA\JsonContent(
     *             @OA\Property(property="data", type="array", @OA\Items(type="object"))
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Error al obtener incapacidades"
     *     )
     * )
     */
    public function index(Request $request)
    {
        try {
            $incapacidades = Incapacidad::with([
                'contrato.hojaDeVida.usuario.user',
                'contrato.area'  // <-- Agregado para cargar área
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
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * @OA\Post(
     *     path="/api/incapacidad",
     *     summary="Registrar una nueva incapacidad",
     *     tags={"Incapacidades"},
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"descrip", "fechaInicio", "fechaFinal", "contratoId"},
     *             @OA\Property(property="descrip", type="string", example="Incapacidad por enfermedad"),
     *             @OA\Property(property="archivo", type="string", format="binary", description="Archivo adjunto"),
     *             @OA\Property(property="fechaInicio", type="string", format="date", example="2024-07-01"),
     *             @OA\Property(property="fechaFinal", type="string", format="date", example="2024-07-10"),
     *             @OA\Property(property="contratoId", type="integer", example=5)
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Incapacidad creada correctamente",
     *         @OA\JsonContent(
     *             @OA\Property(property="mensaje", type="string", example="incapacidad creado correctamente"),
     *             @OA\Property(property="incapacidad", type="object"),
     *             @OA\Property(property="status", type="integer", example=201)
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Error en la validación de datos de la incapacidad"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Error al crear el incapacidad"
     *     )
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

            // crea carpeta si no existe, guarda archivo
            //$path = $file->storeAs($folder, $file->getClientOriginalName(), 'public');
            $extension = $file->getClientOriginalExtension();
            $filename = $request->input('contratoId') . '.' . $extension;
            $path = $file->storeAs($folder, $filename, 'public');

            // guardamos la URL relativa
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
     *     path="/api/incapacidad/{id}",
     *     summary="Obtener una incapacidad por ID",
     *     tags={"Incapacidades"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID de la incapacidad",
     *         required=true,
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Incapacidad encontrada",
     *         @OA\JsonContent(
     *             @OA\Property(property="incapacidad", type="object"),
     *             @OA\Property(property="status", type="integer", example=200)
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Incapacidad no encontrada"
     *     )
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
     * Show the form for editing the specified resource.
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
     *     path="/api/incapacidad/{id}",
     *     summary="Actualizar una incapacidad",
     *     tags={"Incapacidades"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID de la incapacidad",
     *         required=true,
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"descrip", "fechaInicio", "fechaFinal", "contratoId"},
     *             @OA\Property(property="descrip", type="string", example="Actualización de incapacidad"),
     *             @OA\Property(property="archivo", type="string", format="binary"),
     *             @OA\Property(property="fechaInicio", type="string", format="date", example="2024-07-01"),
     *             @OA\Property(property="fechaFinal", type="string", format="date", example="2024-07-10"),
     *             @OA\Property(property="contratoId", type="integer", example=5)
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Incapacidad actualizada correctamente",
     *         @OA\JsonContent(
     *             @OA\Property(property="incapacidad", type="object"),
     *             @OA\Property(property="status", type="integer", example=200)
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Error en la validación"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Incapacidad no encontrada"
     *     )
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

            // crea carpeta si no existe, guarda archivo
            //$path = $file->storeAs($folder, $file->getClientOriginalName(), 'public');
            $extension = $file->getClientOriginalExtension();
            $filename = $request->input('numDocumento') . '.' . $extension;
            $path = $file->storeAs($folder, $filename, 'public');

            // guardamos la URL relativa
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
     *     path="/api/incapacidad/{id}",
     *     summary="Actualizar parcialmente una incapacidad",
     *     tags={"Incapacidades"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID de la incapacidad",
     *         required=true,
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\RequestBody(
     *         required=false,
     *         @OA\JsonContent(
     *             @OA\Property(property="descrip", type="string"),
     *             @OA\Property(property="archivo", type="string", format="binary"),
     *             @OA\Property(property="fechaInicio", type="string", format="date"),
     *             @OA\Property(property="fechaFinal", type="string", format="date"),
     *             @OA\Property(property="contratoId", type="integer")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Incapacidad actualizada parcialmente",
     *         @OA\JsonContent(
     *             @OA\Property(property="incapacidad", type="object"),
     *             @OA\Property(property="status", type="integer", example=200)
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Error en la validación"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Incapacidad no encontrada"
     *     )
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
            'archivo' => 'nullable|file|max:5120',
            'fechaInicio' => 'date',
            'fechaFinal' => 'date',
            'contratoId' => 'integer'
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

        $incapacidad->update($request->only([
            'descrip',
            'archivo',
            'fechaInicio',
            'fechaFinal',
            'contratoId'
        ]));

        return response()->json([
            'mensaje' => 'Incapacidad actualizada parcialmente',
            'incapacidad' => $incapacidad,
            'status' => 200
        ]);
    }
    /**
     * @OA\Put(
     *     path="/api/incapacidad/estado/{id}",
     *     summary="Actualizar estado de una incapacidad",
     *     description="Permite modificar el estado de una incapacidad específica.",
     *     tags={"Incapacidades"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID de la incapacidad",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"estado"},
     *             @OA\Property(property="estado", type="integer", example=1, description="0: Pendiente, 1: Aprobado, 2: Rechazado")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Estado de la incapacidad actualizado correctamente"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Incapacidad no encontrada"
     *     )
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

        $request->validate([
            'estado' => 'required|integer|in:0,1,2'
        ]);

        $incapacidad->estado = $request->estado;
        $incapacidad->save();

        return response()->json([
            'mensaje' => 'Estado actualizado correctamente',
            'estado' => $incapacidad->estado,
            'status' => 200
        ]);
    }
}
