<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Notificacion;
use App\Models\Contrato;
use Illuminate\Support\Facades\Validator;

class NotificacionesController extends Controller
{
    public function index()
    {
        $notificacion = Notificacion::all();
        return response()->json([
            'Notificaciones' => $notificacion,
            'status' => 200
        ], 200);
    }

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
