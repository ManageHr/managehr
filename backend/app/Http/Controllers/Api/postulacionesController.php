<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Postulaciones;
use App\Models\Usuarios;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class PostulacionesController extends Controller
{
    public function index(Request $request)
    {
        try {
            $query = Postulaciones::query();

            if ($request->has('vacantesId')) {
                $query->where('vacantesId', $request->input('vacantesId'));
            }

            $postulaciones = $query->get();

            return response()->json([
                'data' => $postulaciones
            ], 200);
        } catch (\Exception $e) {
            Log::error('Error al obtener lista de postulaciones (PostulacionesController::index): ' . $e->getMessage());
            return response()->json([
                'message' => 'Ocurrió un error al obtener las postulaciones.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function show($idPostulaciones)
    {
        try {
            $postulacion = Postulaciones::find($idPostulaciones);

            if (!$postulacion) {
                return response()->json(['message' => 'Postulación no encontrada'], 404);
            }

            return response()->json([
                'data' => $postulacion
            ], 200);
        } catch (\Exception $e) {
            Log::error('Error al obtener postulación con ID ' . $idPostulaciones . ' (PostulacionesController::show): ' . $e->getMessage());
            return response()->json([
                'message' => 'Ocurrió un error al obtener la postulación.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function searchByVacantesId($vacantesId)
    {
        try {
            if (empty($vacantesId) || !is_numeric($vacantesId)) {
                return response()->json([
                    'message' => 'Parámetro vacantesId inválido o faltante.'
                ], 400);
            }

            $vacantesId = (int) $vacantesId;

            $postulaciones = Postulaciones::where('vacantesId', $vacantesId)->get();

            return response()->json([
                'results' => $postulaciones
            ], 200);
        } catch (\Exception $e) {
            Log::error('Error al buscar postulaciones por Vacante ID (PostulacionesController::searchByVacantesId): ' . $e->getMessage());
            return response()->json([
                'message' => 'Ocurrió un error al realizar la búsqueda.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function updateStatus(Request $request, $idPostulaciones)
    {
        try {
            $postulacion = Postulaciones::find($idPostulaciones);

            if (!$postulacion) {
                return response()->json(['message' => 'Postulación no encontrada'], 404);
            }

            $postulacion->estado = $request->input('estado');
            $postulacion->save();

            return response()->json([
                'message' => 'Estado actualizado correctamente.',
                'data' => $postulacion
            ], 200);
        } catch (\Exception $e) {
            Log::error('Error al actualizar estado de postulación (PostulacionesController::updateStatus): ' . $e->getMessage());
            return response()->json([
                'message' => 'Ocurrió un error al actualizar el estado.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function store(Request $request)
    {
        try {
            $user = Auth::user();

            // Buscar el perfil del usuario autenticado usando el correo
            $usuario = Usuarios::where('email', $user->email)->first();

            if (!$usuario || !$usuario->numDocumento) {
                return response()->json([
                    'message' => 'No se encontró un perfil válido para este usuario.'
                ], 401);
            }

            $postulacion = Postulaciones::create([
                'fechaPostulacion' => Carbon::now(),
                'estado' => 1,
                'vacantesId' => $request->input('vacantesId'),
                'numdocumento' => $usuario->numDocumento,
            ]);

            return response()->json([
                'message' => 'Postulación registrada exitosamente.',
                'data' => $postulacion
            ], 201);

        } catch (\Exception $e) {
            Log::error("Error al registrar postulación: " . $e->getMessage());
            return response()->json([
                'message' => 'Error al registrar la postulación',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
