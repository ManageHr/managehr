<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Vacaciones;
use Illuminate\Support\Facades\Validator;

/**
 * @OA\Tag(
 *     name="Vacaciones",
 *     description="Gestión de solicitudes de vacaciones. Permite crear, consultar, actualizar, eliminar y cambiar el estado de las solicitudes de vacaciones de los empleados."
 * )
 */
class vacacioneController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/vacaciones",
     *     summary="Obtener todas las solicitudes de vacaciones",
     *     tags={"Vacaciones"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Lista de vacaciones obtenida exitosamente",
     *         @OA\JsonContent(
     *             @OA\Property(property="vacaciones", type="array", @OA\Items(type="object")),
     *             @OA\Property(property="status", type="integer", example=200)
     *         )
     *     )
     * )
     */
    public function index()
    {
        $vacaciones = Vacaciones::with([
            'contrato.tipoContrato',
            'contrato.area',
            'contrato.hojaDeVida.usuario'
        ])->get();

        return response()->json([
            'vacaciones' => $vacaciones,
            'status' => 200
        ]);
    }

    /**
     * @OA\Post(
     *     path="/api/vacaciones",
     *     summary="Registrar una nueva solicitud de vacaciones",
     *     tags={"Vacaciones"},
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"motivo", "fechaInicio", "fechaFinal", "dias", "contratoId"},
     *             @OA\Property(property="motivo", type="string", example="Vacaciones familiares"),
     *             @OA\Property(property="fechaInicio", type="string", format="date", example="2025-08-01"),
     *             @OA\Property(property="fechaFinal", type="string", format="date", example="2025-08-10"),
     *             @OA\Property(property="dias", type="integer", example=10),
     *             @OA\Property(property="contratoId", type="integer", example=5)
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Solicitud creada correctamente",
     *         @OA\JsonContent(
     *             @OA\Property(property="mensaje", type="string", example="Vacación creada correctamente"),
     *             @OA\Property(property="vacaciones", type="object"),
     *             @OA\Property(property="status", type="integer", example=201)
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Error de validación"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Error al crear la solicitud"
     *     )
     * )
     */


    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'motivo' => 'required|string|max:500',
            'fechaInicio' => 'required|date',
            'fechaFinal' => 'required|date',
            'dias' => 'required|integer',
            'contratoId' => 'required|integer'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'mensaje' => 'Error en la validación de datos',
                'errors' => $validator->errors(),
                'status' => 400
            ], 400);
        }

        try {
            $vacaciones = Vacaciones::create($request->only([
                'motivo',
                'fechaInicio',
                'fechaFinal',
                'dias',
                'contratoId'
            ]));

            return response()->json([
                'mensaje' => 'Vacación creada correctamente',
                'vacaciones' => $vacaciones,
                'status' => 201
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'mensaje' => 'Error al crear la vacación',
                'error' => $e->getMessage(),
                'status' => 500
            ], 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/vacaciones/{id}",
     *     summary="Obtener una solicitud de vacaciones",
     *     tags={"Vacaciones"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID de la vacación",
     *         required=true,
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Vacación encontrada exitosamente",
     *         @OA\JsonContent(
     *             @OA\Property(property="vacaciones", type="object"),
     *             @OA\Property(property="status", type="integer", example=200)
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Vacación no encontrada"
     *     )
     * )
     */
    public function show($id)
    {
        $vacaciones = Vacaciones::with([
            'contrato.tipoContrato',
            'contrato.area',
            'contrato.hojaDeVida.usuario'
        ])->where('idVacaciones', $id)->first();

        if (!$vacaciones) {
            return response()->json([
                "mensaje" => "Vacación no encontrada",
                "status" => 404
            ], 404);
        }

        return response()->json([
            "vacaciones" => $vacaciones,
            "status" => 200
        ]);
    }

    /**
     * @OA\Put(
     *     path="/api/vacaciones/{id}",
     *     summary="Actualizar una solicitud de vacaciones",
     *     tags={"Vacaciones"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID de la vacación",
     *         required=true,
     *         @OA\Schema(type="integer", example=2)
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"motivo", "fechaInicio", "fechaFinal", "dias", "contratoId"},
     *             @OA\Property(property="motivo", type="string", example="Cambio de fechas"),
     *             @OA\Property(property="fechaInicio", type="string", example="2025-09-01"),
     *             @OA\Property(property="fechaFinal", type="string", example="2025-09-07"),
     *             @OA\Property(property="dias", type="integer", example=7),
     *             @OA\Property(property="contratoId", type="integer", example=4)
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Vacación actualizada correctamente",
     *         @OA\JsonContent(
     *             @OA\Property(property="vacaciones", type="object"),
     *             @OA\Property(property="status", type="integer", example=200)
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Datos inválidos"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Vacación no encontrada"
     *     )
     * )
     */
    public function update(Request $request, $id)
    {
        $vacaciones = Vacaciones::find($id);
        if (!$vacaciones) {
            return response()->json([
                "mensaje" => "Vacación no encontrada",
                "status" => 404
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'motivo' => 'required|string|max:500',
            'fechaInicio' => 'required|date',
            'fechaFinal' => 'required|date',
            'dias' => 'required|integer',
            'contratoId' => 'required|integer'
        ]);

        if ($validator->fails()) {
            return response()->json([
                "errors" => $validator->errors(),
                "status" => 400
            ], 400);
        }

        $vacaciones->update($request->only([
            'motivo',
            'fechaInicio',
            'fechaFinal',
            'dias',
            'contratoId'
        ]));

        return response()->json([
            "vacaciones" => $vacaciones,
            "status" => 200
        ]);
    }

    /**
     * @OA\Patch(
     *     path="/api/vacaciones/{id}",
     *     summary="Actualizar parcialmente una solicitud de vacaciones",
     *     tags={"Vacaciones"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID de la vacación",
     *         required=true,
     *         @OA\Schema(type="integer", example=5)
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="motivo", type="string", example="Vacaciones por salud"),
     *             @OA\Property(property="fechaInicio", type="string", format="date", example="2025-10-01"),
     *             @OA\Property(property="fechaFinal", type="string", format="date", example="2025-10-05"),
     *             @OA\Property(property="dias", type="integer", example=5),
     *             @OA\Property(property="contratoId", type="integer", example=6)
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Vacación actualizada parcialmente",
     *         @OA\JsonContent(
     *             @OA\Property(property="vacaciones", type="object"),
     *             @OA\Property(property="status", type="integer", example=200)
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Error de validación"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Vacación no encontrada"
     *     )
     * )
     */
    public function updatePartial(Request $request, $id)
    {
        $vacaciones = Vacaciones::find($id);
        if (!$vacaciones) {
            return response()->json([
                "mensaje" => "Vacación no encontrada",
                "status" => 404
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'motivo' => 'sometimes|string|max:500',
            'fechaInicio' => 'sometimes|date',
            'fechaFinal' => 'sometimes|date',
            'dias' => 'sometimes|integer',
            'contratoId' => 'sometimes|integer'
        ]);

        if ($validator->fails()) {
            return response()->json([
                "mensaje" => "Error de validación",
                "errors" => $validator->errors(),
                "status" => 400
            ], 400);
        }

        $vacaciones->update($request->only([
            'motivo',
            'fechaInicio',
            'fechaFinal',
            'dias',
            'contratoId'
        ]));

        return response()->json([
            "vacaciones" => $vacaciones,
            "status" => 200
        ]);
    }

    /**
     * @OA\Delete(
     *     path="/api/vacaciones/{id}",
     *     summary="Eliminar una solicitud de vacaciones",
     *     tags={"Vacaciones"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID de la vacación",
     *         required=true,
     *         @OA\Schema(type="integer", example=3)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Vacación eliminada correctamente",
     *         @OA\JsonContent(
     *             @OA\Property(property="mensaje", type="string", example="Vacación eliminada"),
     *             @OA\Property(property="status", type="integer", example=200)
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Vacación no encontrada"
     *     )
     * )
     */
    public function destroy($id)
    {
        $vacaciones = Vacaciones::find($id);
        if (!$vacaciones) {
            return response()->json([
                "mensaje" => "Vacación no encontrada",
                "status" => 404
            ], 404);
        }

        $vacaciones->delete();

        return response()->json([
            "mensaje" => "Vacación eliminada",
            "status" => 200
        ]);
    }

    /**
     * @OA\Patch(
     *     path="/api/vacaciones/{id}/estado",
     *     summary="Actualizar estado de la vacación",
     *     tags={"Vacaciones"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID de la vacación",
     *         required=true,
     *         @OA\Schema(type="integer", example=7)
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"estado"},
     *             @OA\Property(property="estado", type="string", example="Aprobado")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Estado actualizado correctamente",
     *         @OA\JsonContent(
     *             @OA\Property(property="mensaje", type="string", example="Estado actualizado correctamente"),
     *             @OA\Property(property="vacacion", type="object"),
     *             @OA\Property(property="status", type="integer", example=200)
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Estado inválido o no permitido"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Vacación no encontrada"
     *     )
     * )
     */
    public function actualizarEstado(Request $request, $id)
    {
        $estado = ucfirst(strtolower(trim($request->estado))); // <-- limpia el input
        $request->merge(['estado' => $estado]);

        $request->validate([
            'estado' => 'required|in:Pendiente,Aprobado,Rechazado'
        ]);

        $vacacion = Vacaciones::find($id);
        if (!$vacacion) {
            return response()->json(['mensaje' => 'Vacación no encontrada'], 404);
        }

        $vacacion->estado = $estado;
        $vacacion->save();

        return response()->json([
            'mensaje' => 'Estado actualizado correctamente',
            'vacacion' => $vacacion,
            'status' => 200
        ]);
    }
}
