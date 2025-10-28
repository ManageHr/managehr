<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Notificacion;
use App\Models\Contrato;
use Illuminate\Support\Facades\Validator;

/**
 * @OA\Tag(
 *     name="Notificaciones",
 *     description="Gestión de notificaciones del sistema. Permite crear, consultar, actualizar, eliminar y cambiar el estado de las notificaciones."
 * )
 */
class NotificacionesController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/notificaciones",
     *     summary="Obtener todas las notificaciones",
     *     tags={"Notificaciones"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Lista de notificaciones obtenida exitosamente",
     *         @OA\JsonContent(
     *             @OA\Property(property="Notificaciones", type="array", @OA\Items(type="object")),
     *             @OA\Property(property="status", type="integer", example=200)
     *         )
     *     )
     * )
     */
    public function index()
    {
        $notificaciones = Notificacion::with([
            'contrato.area',
            'contrato.hojaDeVida.usuario'
        ])->get();

        return response()->json([
            'Notificaciones' => $notificaciones,
            'status' => 200
        ], 200);
    }

    /**
     * @OA\Put(
     *     path="/api/notificaciones/{id}/estado",
     *     summary="Actualizar el estado de una notificación",
     *     tags={"Notificaciones"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID de la notificación",
     *         required=true,
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="estado", type="integer", example=1)
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Estado actualizado correctamente",
     *         @OA\JsonContent(
     *             @OA\Property(property="mensaje", type="string", example="Estado actualizado correctamente"),
     *             @OA\Property(property="Notificacion", type="object"),
     *             @OA\Property(property="status", type="integer", example=200)
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Error en la validación"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Notificacion no encontrada"
     *     )
     * )
     */
    public function actualizarEstado(Request $request, $id)
    {
        $notificacion = Notificacion::find($id);

        if (!$notificacion) {
            return response()->json([
                'mensaje' => 'Notificación no encontrada',
                'status' => 404
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'estado' => 'required|integer', // o enum si tienes valores fijos
        ]);

        if ($validator->fails()) {
            return response()->json([
                'mensaje' => 'Error en la validación',
                'errors' => $validator->errors(),
                'status' => 400
            ], 400);
        }

        $notificacion->estado = $request->input('estado');
        $notificacion->save();

        return response()->json([
            'mensaje' => 'Estado actualizado correctamente',
            'Notificacion' => $notificacion,
            'status' => 200
        ]);
    }


    /**
     * @OA\Post(
     *     path="/api/notificaciones",
     *     summary="Crear una nueva notificación",
     *     tags={"Notificaciones"},
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"tipo", "accion", "usuarioId", "referenciaId"},
     *             @OA\Property(property="tipo", type="string", enum={"HorasExtra","Vacaciones","Permiso","Postulacion","Rol"}, example="Vacaciones"),
     *             @OA\Property(property="accion", type="string", enum={"Creado","Modificado","Eliminado","EstadoAceptado"}, example="Creado"),
     *             @OA\Property(property="fecha", type="string", format="date", example="2024-06-01"),
     *             @OA\Property(property="detalle", type="string", example="Detalle de la notificación"),
     *             @OA\Property(property="usuarioId", type="integer", example=1),
     *             @OA\Property(property="areaId", type="integer", example=2),
     *             @OA\Property(property="referenciaId", type="integer", example=10)
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Notificación creada correctamente",
     *         @OA\JsonContent(
     *             @OA\Property(property="mensaje", type="string", example="Notificacion creada correctamente"),
     *             @OA\Property(property="Notificaciones", type="object"),
     *             @OA\Property(property="status", type="integer", example=201)
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Error en la validación de datos de notificaciones"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Error al crear Notificacion"
     *     )
     * )
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'tipo' => 'required|in:HorasExtra,Vacaciones,Permiso,Postulacion,Rol',
            'accion' => 'required|in:Creado,Modificado,Eliminado,EstadoAceptado',
            'fecha' => 'nullable|date',
            'detalle' => 'nullable|string|max:1000',
            'usuarioId' => 'required|integer|exists:usuarios,usersId',
            'areaId' => 'nullable|integer|exists:area,idArea',
            'referenciaId' => 'required|integer'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'mensaje' => 'Error en la validación de datos de notificaciones:',
                'errors' => $validator->errors(),
                'status' => 400
            ], 400);
        }

        try {
            $notificacion = Notificacion::create($request->all());

            return response()->json([
                'mensaje' => 'Notificacion creada correctamente',
                'Notificaciones' => $notificacion,
                'status' => 201
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'mensaje' => 'Error al crear Notificacion:',
                'error' => $e->getMessage(),
                'status' => 500
            ], 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/notificaciones/{id}",
     *     summary="Obtener una notificación por ID",
     *     tags={"Notificaciones"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID de la notificación",
     *         required=true,
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Notificación encontrada",
     *         @OA\JsonContent(
     *             @OA\Property(property="Notificacion", type="object"),
     *             @OA\Property(property="status", type="integer", example=200)
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Notificacion no encontrada"
     *     )
     * )
     */
    public function show($id)
    {
        $notificacion = Notificacion::find($id);

        if (!$notificacion) {
            return response()->json([
                'mensaje' => 'Notificacion no encontrada',
                'status' => 404
            ], 404);
        }

        return response()->json([
            'Notificacion' => $notificacion,
            'status' => 200
        ], 200);
    }

    /**
     * @OA\Put(
     *     path="/api/notificaciones/{id}",
     *     summary="Actualizar una notificación",
     *     tags={"Notificaciones"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID de la notificación",
     *         required=true,
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"tipo", "accion", "usuarioId", "referenciaId"},
     *             @OA\Property(property="tipo", type="string", enum={"HorasExtra","Vacaciones","Permiso","Postulacion","Rol"}, example="Vacaciones"),
     *             @OA\Property(property="accion", type="string", enum={"Creado","Modificado","Eliminado","EstadoAceptado"}, example="Modificado"),
     *             @OA\Property(property="fecha", type="string", format="date", example="2024-06-01"),
     *             @OA\Property(property="detalle", type="string", example="Detalle actualizado"),
     *             @OA\Property(property="usuarioId", type="integer", example=1),
     *             @OA\Property(property="areaId", type="integer", example=2),
     *             @OA\Property(property="referenciaId", type="integer", example=10)
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Notificación actualizada correctamente",
     *         @OA\JsonContent(
     *             @OA\Property(property="mensaje", type="string", example="Notificacion actualizada correctamente"),
     *             @OA\Property(property="Notificaciones", type="object"),
     *             @OA\Property(property="status", type="integer", example=200)
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Error en la validación"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Notificacion no encontrada"
     *     )
     * )
     */
    public function update(Request $request, $id)
    {
        $notificacion = Notificacion::find($id);

        if (!$notificacion) {
            return response()->json([
                'mensaje' => 'Notificacion no encontrada',
                'status' => 404
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'tipo' => 'required|in:HorasExtra,Vacaciones,Permiso,Postulacion,Rol',
            'accion' => 'required|in:Creado,Modificado,Eliminado,EstadoAceptado',
            'fecha' => 'nullable|date',
            'detalle' => 'nullable|string|max:1000',
            'usuarioId' => 'required|integer|exists:usuarios,usersId',
            'areaId' => 'nullable|integer|exists:area,idArea',
            'referenciaId' => 'required|integer'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'mensaje' => 'Error en la validación',
                'errors' => $validator->errors(),
                'status' => 400
            ], 400);
        }

        $notificacion->update($request->all());

        return response()->json([
            'mensaje' => 'Notificacion actualizada correctamente',
            'Notificaciones' => $notificacion,
            'status' => 200
        ]);
    }

    /**
     * @OA\Patch(
     *     path="/api/notificaciones/{id}",
     *     summary="Actualizar parcialmente una notificación",
     *     tags={"Notificaciones"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID de la notificación",
     *         required=true,
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\RequestBody(
     *         required=false,
     *         @OA\JsonContent(
     *             @OA\Property(property="tipo", type="string", enum={"HorasExtra","Vacaciones","Permiso","Postulacion","Rol"}),
     *             @OA\Property(property="accion", type="string", enum={"Creado","Modificado","Eliminado","EstadoAceptado"}),
     *             @OA\Property(property="fecha", type="string", format="date"),
     *             @OA\Property(property="detalle", type="string"),
     *             @OA\Property(property="usuarioId", type="integer"),
     *             @OA\Property(property="areaId", type="integer"),
     *             @OA\Property(property="referenciaId", type="integer")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Notificación actualizada parcialmente",
     *         @OA\JsonContent(
     *             @OA\Property(property="mensaje", type="string", example="Notificacion actualizada parcialmente"),
     *             @OA\Property(property="Notificaciones", type="object"),
     *             @OA\Property(property="status", type="integer", example=200)
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Error en la validación"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Notificacion no encontrada"
     *     )
     * )
     */
    public function updatePartial(Request $request, $id)
    {
        $notificacion = Notificacion::find($id);

        if (!$notificacion) {
            return response()->json([
                'mensaje' => 'Notificacion no encontrada',
                'status' => 404
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'tipo' => 'in:HorasExtra,Vacaciones,Permiso,Postulacion,Rol',
            'accion' => 'in:Creado,Modificado,Eliminado,EstadoAceptado',
            'fecha' => 'nullable|date',
            'detalle' => 'nullable|string|max:1000',
            'usuarioId' => 'integer|exists:usuarios,usersId',
            'areaId' => 'nullable|integer|exists:areas,idArea',
            'referenciaId' => 'integer'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'mensaje' => 'Error en la validación',
                'errors' => $validator->errors(),
                'status' => 400
            ], 400);
        }
        $contrato = Contrato::find($request->input('contratoId'));
        $areaNueva = $contrato ? $contrato->area : null;

        $notificacion->update([
            'accion' => 'Modificado',
            'detalle' => 'Detalle actualizado',
            'areaId' => $areaNueva
        ]);


        return response()->json([
            'mensaje' => 'Horas extra actualizada parcialmente',
            'Notificaciones' => $notificacion,
            'status' => 200
        ]);
    }

    /**
     * @OA\Delete(
     *     path="/api/notificaciones/{id}",
     *     summary="Eliminar una notificación",
     *     tags={"Notificaciones"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID de la notificación",
     *         required=true,
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Notificación eliminada correctamente",
     *         @OA\JsonContent(
     *             @OA\Property(property="mensaje", type="string", example="Notificación eliminada correctamente"),
     *             @OA\Property(property="status", type="integer", example=200)
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Notificacion no encontrada"
     *     )
     * )
     */
    public function destroy($id)
    {
        $notificacion = Notificacion::find($id);

        if (!$notificacion) {
            return response()->json([
                'mensaje' => 'Horas extra no encontrada',
                'status' => 404
            ], 404);
        }

        $notificacion->delete();

        return response()->json([
            'mensaje' => 'Horas extra eliminada correctamente',
            'status' => 200
        ]);
    }
}
