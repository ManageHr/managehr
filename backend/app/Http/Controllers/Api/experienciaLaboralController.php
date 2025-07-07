<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\ExperienciaLaboral;
use App\Models\HojasVidaHasExperiencia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;

/**
 * @OA\Tag(
 *     name="Experiencia Laboral",
 *     description="Gestión de las experiencias laborales registradas de los empleados "
 * )
 */

class experienciaLaboralController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/experienciaLaboral",
     *     summary="Listar todas las experiencias laborales",
     *     tags={"Experiencia Laboral"},
     *     @OA\Response(
     *         response=200,
     *         description="Listado de experiencias laborales de los empleados "
     *     )
     * )
     */

    public function index()
    {
        $experiencias = ExperienciaLaboral::all();
        return response()->json([
            "experiencias" => $experiencias,
            "status" => 200
        ]);
    }
    /**
     * @OA\Post(
     *     path="/api/experienciaLaboral",
     *     summary="Crear nueva experiencia laboral",
     *     tags={"Experiencia Laboral"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"nomEmpresa", "nomJefe", "telefono", "cargo", "actividades", "fechaInicio", "fechaFinalizacion"},
     *             @OA\Property(property="nomEmpresa", type="string", maxLength=45),
     *             @OA\Property(property="nomJefe", type="string", maxLength=45),
     *             @OA\Property(property="telefono", type="integer"),
     *             @OA\Property(property="cargo", type="string", maxLength=20),
     *             @OA\Property(property="actividades", type="string"),
     *             @OA\Property(property="fechaInicio", type="string", format="date"),
     *             @OA\Property(property="fechaFinalizacion", type="string", format="date")
     *         )
     *     ),
     *     @OA\Response(response=201, description="Experiencia creada"),
     *     @OA\Response(response=400, description="Error de validación")
     * )
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nomEmpresa' => 'required|string|max:45',
            'nomJefe' => 'required|string|max:45',
            'telefono' => 'required|integer',
            'cargo' => 'required|string|max:20',
            'actividades' => 'required|string',
            'fechaInicio' => 'required|date',
            'fechaFinalizacion' => 'required|date|after_or_equal:fechaInicio'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'mensaje' => 'Error en la validación de datos de experiencia',
                'errors' => $validator->errors(),
                'status' => 400
            ], 400);
        }

        try {
            $experiencia = ExperienciaLaboral::create($request->all());
            return response()->json([
                'mensaje' => 'Experiencia registrada correctamente',
                'experiencia' => $experiencia,
                'status' => 201
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'mensaje' => 'Error al registrar la experiencia',
                'error' => $e->getMessage(),
                'status' => 500
            ], 500);
        }
    }
    /**
     * @OA\Post(
     *     path="/api/experienciaLaboral/archivo",
     *     summary="Registrar experiencia laboral con archivo y relación con hoja de vida",
     *     tags={"Experiencia Laboral"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 required={"nomEmpresa", "nomJefe", "telefono", "cargo", "fechaInicio", "fechaFinalizacion", "idHojaDevida"},
     *                 @OA\Property(property="nomEmpresa", type="string"),
     *                 @OA\Property(property="nomJefe", type="string"),
     *                 @OA\Property(property="telefono", type="number"),
     *                 @OA\Property(property="cargo", type="string"),
     *                 @OA\Property(property="actividades", type="string"),
     *                 @OA\Property(property="fechaInicio", type="string", format="date"),
     *                 @OA\Property(property="fechaFinalizacion", type="string", format="date"),
     *                 @OA\Property(property="idHojaDevida", type="integer"),
     *                 @OA\Property(property="archivo", type="string", format="binary")
     *             )
     *         )
     *     ),
     *     @OA\Response(response=201, description="Experiencia con archivo registrada correctamente"),
     *     @OA\Response(response=400, description="Error de validación")
     * )
     */

    public function storeConArchivo(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nomEmpresa' => 'required|string|max:45',
            'nomJefe' => 'required|string|max:45',
            'telefono' => 'required|numeric',
            'cargo' => 'required|string|max:20',
            'actividades' => 'nullable|string',
            'fechaInicio' => 'required|date',
            'fechaFinalizacion' => 'required|date|after_or_equal:fechaInicio',
            'idHojaDevida' => 'required|exists:hojasvida,idHojaDeVida',
            'archivo' => 'nullable|file|max:2048'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'mensaje' => 'Error en la validación con archivo',
                'errors' => $validator->errors(),
                'status' => 400
            ], 400);
        }

        try {
            // 1. Crear la experiencia
            $experiencia = ExperienciaLaboral::create($request->only([
                'nomEmpresa',
                'nomJefe',
                'telefono',
                'cargo',
                'actividades',
                'fechaInicio',
                'fechaFinalizacion'
            ]));

            // 2. Manejar archivo si viene
            $archivoPath = null;
            if ($request->hasFile('archivo')) {
                $archivoPath = $request->file('archivo')->store('archivos/experiencias', 'public');
            }

            // 3. Crear la relación con hoja de vida
            HojasVidaHasExperiencia::create([
                'idHojaDevida' => $request->idHojaDevida,
                'idExperiencia' => $experiencia->idExperiencia,
                'archivo' => $archivoPath,
                'estado' => true
            ]);

            return response()->json([
                'mensaje' => 'Experiencia con archivo registrada correctamente',
                'data' => $experiencia,
                'status' => 201
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'mensaje' => 'Error al guardar experiencia con archivo',
                'error' => $e->getMessage(),
                'status' => 500
            ], 500);
        }
    }
    /**
     * @OA\Get(
     *     path="/api/experienciaLaboral/{id}",
     *     summary="Obtener una experiencia laboral por ID",
     *     tags={"Experiencia Laboral"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID de la experiencia",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(response=200, description="Experiencia encontrada"),
     *     @OA\Response(response=404, description="Experiencia no encontrada")
     * )
     */

    public function show($id)
    {
        $experiencia = ExperienciaLaboral::find($id);
        if (!$experiencia) {
            return response()->json([
                'mensaje' => 'Experiencia no encontrada',
                'status' => 404
            ]);
        }

        return response()->json([
            'experiencia' => $experiencia,
            'status' => 200
        ]);
    }
    /**
     * @OA\Put(
     *     path="/api/experienciaLaboral/{id}",
     *     summary="Actualizar una experiencia laboral",
     *     tags={"Experiencia Laboral"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID de la experiencia",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"nomEmpresa", "nombJefe", "telefono", "cargo", "actividades", "fechaInicio", "fechaFinalizacion"},
     *             @OA\Property(property="nomEmpresa", type="string"),
     *             @OA\Property(property="nombJefe", type="string"),
     *             @OA\Property(property="telefono", type="number"),
     *             @OA\Property(property="cargo", type="string"),
     *             @OA\Property(property="actividades", type="string"),
     *             @OA\Property(property="fechaInicio", type="string", format="date"),
     *             @OA\Property(property="fechaFinalizacion", type="string", format="date")
     *         )
     *     ),
     *     @OA\Response(response=200, description="Experiencia actualizada correctamente"),
     *     @OA\Response(response=400, description="Error de validación")
     * )
     */


    public function update(Request $request, $id)
    {
        $experiencia = ExperienciaLaboral::find($id);
        if (!$experiencia) {
            return response()->json([
                'mensaje' => 'Experiencia no encontrada',
                'status' => 404
            ]);
        }

        $validator = Validator::make($request->all(), [
            'nomEmpresa' => 'required|string|max:45',
            'nombJefe' => 'required|string|max:45',
            'telefono' => 'required|numeric|digits_between:7,11',
            'cargo' => 'required|string|max:20',
            'actividades' => 'required|string',
            'fechaInicio' => 'required|date',
            'fechaFinalizacion' => 'required|date|after_or_equal:fechaInicio'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'mensaje' => 'Error en la validación',
                'errors' => $validator->errors(),
                'status' => 400
            ]);
        }

        $experiencia->update($request->all());

        return response()->json([
            'mensaje' => 'Experiencia actualizada correctamente',
            'experiencia' => $experiencia,
            'status' => 200
        ]);
    }
    /**
     * @OA\Delete(
     *     path="/api/experienciaLaboral/{id}",
     *     summary="Eliminar una experiencia laboral",
     *     tags={"Experiencia Laboral"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID de la experiencia",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(response=200, description="Experiencia eliminada correctamente"),
     *     @OA\Response(response=404, description="Experiencia no encontrada")
     * )
     */


    public function destroy($id)
    {
        $experiencia = ExperienciaLaboral::find($id);
        if (!$experiencia) {
            return response()->json([
                'mensaje' => 'Experiencia no encontrada',
                'status' => 404
            ]);
        }

        $experiencia->delete();
        return response()->json([
            'mensaje' => 'Experiencia eliminada correctamente',
            'status' => 200
        ]);
    }
    /**
     * @OA\Patch(
     *     path="/api/experienciaLaboral/{id}",
     *     summary="Actualizar parcialmente una experiencia laboral",
     *     tags={"Experiencia Laboral"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID de la experiencia",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=false,
     *         @OA\JsonContent(
     *             @OA\Property(property="nomEmpresa", type="string"),
     *             @OA\Property(property="nombJefe", type="string"),
     *             @OA\Property(property="telefono", type="number"),
     *             @OA\Property(property="cargo", type="string"),
     *             @OA\Property(property="actividades", type="string"),
     *             @OA\Property(property="fechaInicio", type="string", format="date"),
     *             @OA\Property(property="fechaFinalizacion", type="string", format="date")
     *         )
     *     ),
     *     @OA\Response(response=200, description="Experiencia actualizada parcialmente"),
     *     @OA\Response(response=400, description="Error en la validación parcial")
     * )
     */

    public function updatePartial(Request $request, $id)
    {
        $experiencia = ExperienciaLaboral::find($id);
        if (!$experiencia) {
            return response()->json([
                'mensaje' => 'Experiencia no encontrada',
                'status' => 404
            ]);
        }

        $validator = Validator::make($request->all(), [
            'nomEmpresa' => 'string|max:45',
            'nombJefe' => 'string|max:45',
            'telefono' => 'numeric|digits_between:7,11',
            'cargo' => 'string|max:20',
            'actividades' => 'string',
            'fechaInicio' => 'date',
            'fechaFinalizacion' => 'date|after_or_equal:fechaInicio'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'mensaje' => 'Error en la validación parcial',
                'errors' => $validator->errors(),
                'status' => 400
            ]);
        }

        $experiencia->fill($request->all());
        $experiencia->save();

        return response()->json([
            'mensaje' => 'Experiencia actualizada parcialmente',
            'experiencia' => $experiencia,
            'status' => 200
        ]);
    }
}
