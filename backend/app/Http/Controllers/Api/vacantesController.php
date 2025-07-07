<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Vacantes;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;

class vacantesController extends Controller
{
        /**
     * @OA\Get(
     *     path="/api/vacantes",
     *     summary="Obtener todas las vacantes",
     *     description="Devuelve un listado de todas las vacantes disponibles.",
     *     tags={"Vacantes"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Lista de vacantes obtenida correctamente"
     *     )
     * )
     */

    public function index()
    {
        $vacantes = Vacantes::all();
        return response()->json($vacantes, Response::HTTP_OK);
    }

        /**
     * @OA\Post(
     *     path="/api/vacantes",
     *     summary="Crear una nueva vacante",
     *     description="Crea una nueva vacante con los datos proporcionados.",
     *     tags={"Vacantes"},
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"nomVacante"},
     *             @OA\Property(property="nomVacante", type="string", example="Desarrollador Backend"),
     *             @OA\Property(property="descripVacante", type="string", example="Responsable del desarrollo de APIs."),
     *             @OA\Property(property="salario", type="number", example=4000000),
     *             @OA\Property(property="expMinima", type="string", example="2 años"),
     *             @OA\Property(property="cargoVacante", type="string", example="Programador"),
     *             @OA\Property(property="catVacId", type="integer", example=1)
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Vacante creada correctamente"
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Error de validación"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Error interno del servidor"
     *     )
     * )
     */

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nomVacante' => 'required|string|max:30',
            'descripVacante' => 'nullable|string',
            'salario' => 'nullable|numeric',
            'expMinima' => 'nullable|string|max:45',
            'cargoVacante' => 'nullable|string|max:45',
            'catVacId' => 'nullable|integer',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Error de validación',
                'errors' => $validator->errors()
            ], Response::HTTP_BAD_REQUEST);
        }

        try {
            $vacante = Vacantes::create($request->all());
            return response()->json($vacante, Response::HTTP_CREATED);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error interno del servidor al crear la vacante',
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

        /**
     * @OA\Get(
     *     path="/api/vacantes/{id}",
     *     summary="Obtener una vacante por ID",
     *     description="Devuelve la información de una vacante específica.",
     *     tags={"Vacantes"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID de la vacante",
     *         required=true,
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Vacante encontrada correctamente"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Vacante no encontrada"
     *     )
     * )
     */

    public function show($id)
    {
        $vacante = Vacantes::find($id);

        if ($vacante) {
            return response()->json($vacante, Response::HTTP_OK);
        } else {
            return response()->json(['message' => 'Vacante no encontrada'], Response::HTTP_NOT_FOUND);
        }
    }

        /**
     * @OA\Put(
     *     path="/api/vacantes/{id}",
     *     summary="Actualizar vacante",
     *     description="Actualiza una vacante existente con los datos proporcionados.",
     *     tags={"Vacantes"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID de la vacante",
     *         required=true,
     *         @OA\Schema(type="integer", example=2)
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"nomVacante"},
     *             @OA\Property(property="nomVacante", type="string", example="Analista de Datos"),
     *             @OA\Property(property="descripVacante", type="string", example="Análisis y procesamiento de datos."),
     *             @OA\Property(property="salario", type="number", example=3500000)
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Vacante actualizada correctamente"
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Error de validación"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Vacante no encontrada"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Error interno del servidor"
     *     )
     * )
     */

    public function update(Request $request, $id)
    {
        $vacante = Vacantes::find($id);

        if (!$vacante) {
            return response()->json(['message' => 'Vacante no encontrada'], Response::HTTP_NOT_FOUND);
        }

        $validator = Validator::make($request->all(), [
            'nomVacante' => 'required|string|max:30',
            'descripVacante' => 'nullable|string',
            'salario' => 'nullable|numeric',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Error de validación',
                'errors' => $validator->errors()
            ], Response::HTTP_BAD_REQUEST);
        }

        try {
            $vacante->update($request->all());
            return response()->json($vacante, Response::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error interno del servidor al actualizar la vacante',
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

        /**
     * @OA\Delete(
     *     path="/api/vacantes/{id}",
     *     summary="Eliminar una vacante",
     *     description="Elimina una vacante existente por su ID.",
     *     tags={"Vacantes"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID de la vacante",
     *         required=true,
     *         @OA\Schema(type="integer", example=3)
     *     ),
     *     @OA\Response(
     *         response=204,
     *         description="Vacante eliminada correctamente"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Vacante no encontrada"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Error interno del servidor"
     *     )
     * )
     */


    public function destroy($id)
    {
        $vacante = Vacantes::find($id);

        if (!$vacante) {
            return response()->json(['message' => 'Vacante no encontrada'], Response::HTTP_NOT_FOUND);
        }

        try {
            $vacante->delete();
            return response()->json(null, Response::HTTP_NO_CONTENT);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error interno del servidor al eliminar la vacante',
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
