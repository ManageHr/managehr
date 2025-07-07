<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Notificacion;
use App\Models\Contrato;
use Illuminate\Support\Facades\Validator;
use OpenApi\Annotations as OA;

class NotificacionesController extends Controller
{

      /**
     * @OA\Get(
     *     path="/api/notificaciones",
     *     summary="Listar todas las notificaciones",
     *     description="Obtiene una lista de todas las notificaciones con sus relaciones (área, usuario).",
     *     tags={"Notificaciones"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(response=200, description="Listado obtenido correctamente")
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
     * @OA\Patch(
     *     path="/api/notificaciones/estado/{id}",
     *     summary="Actualizar solo el estado de una notificación",
     *     description="Modifica el campo 'estado' de una notificación específica.",
     *     tags={"Notificaciones"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID de la notificación",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         @OA\JsonContent(
     *             required={"estado"},
     *             @OA\Property(property="estado", type="integer", example=1)
     *         )
     *     ),
     *     @OA\Response(response=200, description="Estado actualizado correctamente"),
     *     @OA\Response(response=404, description="Notificación no encontrada")
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
     *     description="Registra una nueva notificación para una acción específica.",
     *     tags={"Notificaciones"},
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"tipo", "accion", "usuarioId", "referenciaId"},
     *             @OA\Property(property="tipo", type="string", enum={"HorasExtra", "Vacaciones", "Permiso", "Postulacion", "Rol"}),
     *             @OA\Property(property="accion", type="string", enum={"Creado", "Modificado", "Eliminado", "EstadoAceptado"}),
     *             @OA\Property(property="fecha", type="string", format="date", nullable=true),
     *             @OA\Property(property="detalle", type="string", nullable=true),
     *             @OA\Property(property="usuarioId", type="integer"),
     *             @OA\Property(property="areaId", type="integer", nullable=true),
     *             @OA\Property(property="referenciaId", type="integer")
     *         )
     *     ),
     *     @OA\Response(response=201, description="Notificación creada correctamente"),
     *     @OA\Response(response=400, description="Error de validación")
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
     *     summary="Consultar notificación por ID",
     *     description="Devuelve una notificación específica por su ID.",
     *     tags={"Notificaciones"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID de la notificación",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(response=200, description="Notificación encontrada"),
     *     @OA\Response(response=404, description="No encontrada")
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
     *     summary="Actualizar notificación completamente",
     *     description="Modifica todos los campos de una notificación existente.",
     *     tags={"Notificaciones"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"tipo", "accion", "usuarioId", "referenciaId"},
     *             @OA\Property(property="tipo", type="string", enum={"HorasExtra", "Vacaciones", "Permiso", "Postulacion", "Rol"}),
     *             @OA\Property(property="accion", type="string", enum={"Creado", "Modificado", "Eliminado", "EstadoAceptado"}),
     *             @OA\Property(property="fecha", type="string", format="date", nullable=true),
     *             @OA\Property(property="detalle", type="string", nullable=true),
     *             @OA\Property(property="usuarioId", type="integer"),
     *             @OA\Property(property="areaId", type="integer", nullable=true),
     *             @OA\Property(property="referenciaId", type="integer")
     *         )
     *     ),
     *     @OA\Response(response=200, description="Notificación actualizada correctamente"),
     *     @OA\Response(response=400, description="Error de validación")
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
     *     summary="Actualizar notificación parcialmente",
     *     description="Modifica uno o más campos de una notificación.",
     *     tags={"Notificaciones"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\RequestBody(
     *         @OA\JsonContent(
     *             @OA\Property(property="tipo", type="string", enum={"HorasExtra", "Vacaciones", "Permiso", "Postulacion", "Rol"}),
     *             @OA\Property(property="accion", type="string", enum={"Creado", "Modificado", "Eliminado", "EstadoAceptado"}),
     *             @OA\Property(property="fecha", type="string", format="date"),
     *             @OA\Property(property="detalle", type="string"),
     *             @OA\Property(property="usuarioId", type="integer"),
     *             @OA\Property(property="areaId", type="integer"),
     *             @OA\Property(property="referenciaId", type="integer"),
     *             @OA\Property(property="contratoId", type="integer", example=3)
     *         )
     *     ),
     *     @OA\Response(response=200, description="Actualización parcial exitosa"),
     *     @OA\Response(response=404, description="No encontrada")
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
     *     summary="Eliminar notificación",
     *     description="Elimina una notificación existente por su ID.",
     *     tags={"Notificaciones"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Eliminación exitosa"),
     *     @OA\Response(response=404, description="No encontrada")
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
