<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Horasextra;
use Illuminate\Support\Facades\Validator;

/**
 * @OA\Tag(
 *     name="Horas Extra",
 *     description="Solicitudes de horas extra. Permite registrar, consultar, actualizar y eliminar solicitudes propias de horas extra."
 * )
 */
class HorasextraController extends Controller
{

    /**
     * @OA\Get(
     *     path="/api/horasextra",
     *     summary="Listar todas las solicitudes de horas extra",
     *     description="Retorna un listado de todas las horas extra con sus relaciones.",
     *     tags={"Horas Extra"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Listado obtenido con éxito"
     *     )
     * )
     */

    public function index()
    {
        $horas = HorasExtra::with([
            'tipoHoraExtra',
            'contrato.hojaDeVida.usuario.user.rol',
            'contrato.area'
        ])->get();

        return response()->json([
            'data' => $horas,
            'status' => 200
        ], 200);
    }


    /**
     * @OA\Post(
     *     path="/api/horasextra",
     *     summary="Crear una nueva solicitud de horas extra",
     *     description="Registra una nueva solicitud de horas extra.",
     *     tags={"Horas Extra"},
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"descrip", "fecha", "nHorasExtra", "tipoHorasid", "contratoId"},
     *             @OA\Property(property="descrip", type="string", maxLength=500, example="Apoyo en evento institucional"),
     *             @OA\Property(property="fecha", type="string", format="date", example="2025-07-06"),
     *             @OA\Property(property="nHorasExtra", type="number", example=3),
     *             @OA\Property(property="tipoHorasid", type="integer", example=2),
     *             @OA\Property(property="contratoId", type="integer", example=5)
     *         )
     *     ),
     *     @OA\Response(response=201, description="Horas extra creada correctamente"),
     *     @OA\Response(response=400, description="Error de validación"),
     *     @OA\Response(response=500, description="Error interno del servidor")
     * )
     */


    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'descrip' => 'required|string|max:500',
            'fecha' => 'required|date',
            'nHorasExtra' => 'required|numeric|min:0',
            'tipoHorasid' => 'required|integer',
            'contratoId' => 'required|integer'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'mensaje' => 'Error en la validación de datos de horas extra:',
                'errors' => $validator->errors(),
                'status' => 400
            ], 400);
        }

        try {
            $horas = Horasextra::create($request->all());

            return response()->json([
                'mensaje' => 'Horas extra creada correctamente',
                'horasextra' => $horas,
                'status' => 201
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'mensaje' => 'Error al crear horas extra:',
                'error' => $e->getMessage(),
                'status' => 500
            ], 500);
        }
    }


    /**
     * @OA\Get(
     *     path="/api/horasextra/{id}",
     *     summary="Obtener una solicitud específica de horas extra",
     *     description="Devuelve la información de una solicitud de horas extra según el ID.",
     *     tags={"Horas Extra"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID de la solicitud",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(response=200, description="Solicitud encontrada"),
     *     @OA\Response(response=404, description="Solicitud no encontrada")
     * )
     */



    public function show($id)
    {
        $horas = Horasextra::find($id);

        if (!$horas) {
            return response()->json([
                'mensaje' => 'Horas extra no encontrada',
                'status' => 404
            ], 404);
        }

        return response()->json([
            'horasextra' => $horas,
            'status' => 200
        ], 200);
    }


    /**
     * @OA\Put(
     *     path="/api/horasextra/{id}",
     *     summary="Actualizar una solicitud de horas extra",
     *     description="Modifica completamente una solicitud existente.",
     *     tags={"Horas Extra"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID de la solicitud",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"descrip", "fecha", "nHorasExtra", "tipoHorasid", "contratoId"},
     *             @OA\Property(property="descrip", type="string", example="Cambio de turno solicitado"),
     *             @OA\Property(property="fecha", type="string", format="date", example="2025-07-10"),
     *             @OA\Property(property="nHorasExtra", type="number", example=4),
     *             @OA\Property(property="tipoHorasid", type="integer", example=1),
     *             @OA\Property(property="contratoId", type="integer", example=8)
     *         )
     *     ),
     *     @OA\Response(response=200, description="Actualización exitosa"),
     *     @OA\Response(response=400, description="Error de validación"),
     *     @OA\Response(response=404, description="Solicitud no encontrada")
     * )
     */


    public function update(Request $request, $id)
    {
        $horas = Horasextra::find($id);

        if (!$horas) {
            return response()->json([
                'mensaje' => 'Horas extra no encontrada',
                'status' => 404
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'descrip' => 'required|string|max:500',
            'fecha' => 'required|date',
            'nHorasExtra' => 'required|numeric|min:0',
            'tipoHorasid' => 'required|integer',
            'contratoId' => 'required|integer'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'mensaje' => 'Error en la validación',
                'errors' => $validator->errors(),
                'status' => 400
            ], 400);
        }

        $horas->update($request->all());

        return response()->json([
            'mensaje' => 'Horas extra actualizada correctamente',
            'horasextra' => $horas,
            'status' => 200
        ]);
    }


    /**
     * @OA\Patch(
     *     path="/api/horasextra/{id}",
     *     summary="Actualizar parcialmente una solicitud de horas extra",
     *     description="Modifica solo algunos campos de la solicitud.",
     *     tags={"Horas Extra"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID de la solicitud",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         @OA\JsonContent(
     *             @OA\Property(property="descrip", type="string", example="Nueva descripción"),
     *             @OA\Property(property="fecha", type="string", format="date", example="2025-07-08"),
     *             @OA\Property(property="nHorasExtra", type="number", example=2),
     *             @OA\Property(property="tipoHorasid", type="integer", example=3),
     *             @OA\Property(property="contratoId", type="integer", example=6)
     *         )
     *     ),
     *     @OA\Response(response=200, description="Actualización parcial exitosa"),
     *     @OA\Response(response=400, description="Error de validación"),
     *     @OA\Response(response=404, description="Solicitud no encontrada")
     * )
     */



    public function updatePartial(Request $request, $id)
    {
        $horas = Horasextra::find($id);

        if (!$horas) {
            return response()->json([
                'mensaje' => 'Horas extra no encontrada',
                'status' => 404
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'descrip' => 'string|max:500',
            'fecha' => 'date',
            'nHorasExtra' => 'numeric|min:0',
            'tipoHorasid' => 'integer',
            'contratoId' => 'integer'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'mensaje' => 'Error en la validación',
                'errors' => $validator->errors(),
                'status' => 400
            ], 400);
        }

        $horas->update($request->only([
            'descrip',
            'fecha',
            'nHorasExtra',
            'tipoHorasid',
            'contratoId'
        ]));

        return response()->json([
            'mensaje' => 'Horas extra actualizada parcialmente',
            'horasextra' => $horas,
            'status' => 200
        ]);
    }


    /**
     * @OA\Delete(
     *     path="/api/horasextra/{id}",
     *     summary="Eliminar una solicitud de horas extra",
     *     description="Elimina permanentemente una solicitud por ID.",
     *     tags={"Horas Extra"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID de la solicitud a eliminar",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(response=200, description="Solicitud eliminada correctamente"),
     *     @OA\Response(response=404, description="Solicitud no encontrada")
     * )
     */


    public function destroy($id)
    {
        $horas = Horasextra::find($id);

        if (!$horas) {
            return response()->json([
                'mensaje' => 'Horas extra no encontrada',
                'status' => 404
            ], 404);
        }

        $horas->delete();

        return response()->json([
            'mensaje' => 'Horas extra eliminada correctamente',
            'status' => 200
        ]);
    }
    /**
     * @OA\Put(
     *     path="/api/horasextra/estado/{id}",
     *     summary="Actualizar estado de una solicitud de horas extra",
     *     description="Permite cambiar el estado de una solicitud de horas extra.",
     *     tags={"Horas Extra"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID de la solicitud a actualizar",
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
     *         description="Estado actualizado correctamente"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Solicitud no encontrada"
     *     )
     * )
     */
    public function cambiarEstado(Request $request, $id)
    {
        $horas = Horasextra::find($id);

        if (!$horas) {
            return response()->json([
                'mensaje' => 'Solicitud no encontrada',
                'status' => 404
            ], 404);
        }

        $request->validate([
            'estado' => 'required|integer|in:0,1,2'
        ]);

        $horas->estado = $request->estado;
        $horas->save();

        return response()->json([
            'mensaje' => 'Estado actualizado correctamente',
            'estado' => $horas->estado,
            'status' => 200
        ]);
    }
}
