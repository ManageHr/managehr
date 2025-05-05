<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Postulaciones; // Asegúrate de que el nombre del modelo sea correcto (Postulaciones en plural)
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon; // Importar Carbon para las fechas

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
            Log::error('Error al obtener lista de postulaciones (Api\PostulacionesController::index): ' . $e->getMessage());
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
            Log::error('Error al obtener postulación con ID ' . $idPostulaciones . ' (Api\PostulacionesController::show): ' . $e->getMessage());
            return response()->json([
                'message' => 'Ocurrió un error al obtener la postulación.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // === MÉTODO searchByVacantesId (NO MODIFICADO) ===
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
            Log::error('Error al buscar postulaciones por Vacante ID (Api\PostulacionesController::searchByVacantesId): ' . $e->getMessage());
            return response()->json([
                'message' => 'Ocurrió un error al realizar la búsqueda.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // === MÉTODO updateStatus (NO MODIFICADO) ===
    public function updateStatus(Request $request, $idPostulaciones)
    {
        try {
            $postulacion = Postulaciones::find($idPostulaciones);

            if (!$postulacion) {
                return response()->json(['message' => 'Postulación no encontrada'], 404);
            }

            $validator = Validator::make($request->all(), [
                'estado' => ['required', 'string', Rule::in(['Pendiente', 'Aceptado', 'Rechazado'])],
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'message' => 'Error de validación del estado',
                    'errors' => $validator->errors()
                ], 400);
            }

            $nuevoEstadoString = $request->input('estado');
            $nuevoEstadoTinyint = $this->mapEstadoToTinyint($nuevoEstadoString);

            $postulacion->estado = $nuevoEstadoTinyint;
            $postulacion->save();

            return response()->json([
                'message' => 'Estado de postulación actualizado con éxito',
                'postulacion' => $postulacion
            ], 200);
        } catch (\Exception $e) {
            Log::error('Error al actualizar estado de postulación con ID ' . $idPostulaciones . ' (Api\PostulacionesController::updateStatus): ' . $e->getMessage());
            return response()->json([
                'message' => 'Ocurrió un error al actualizar el estado de la postulación.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // === MÉTODO mapEstadoToTinyint (NO MODIFICADO) ===
    private function mapEstadoToTinyint(string $estadoString): int
    {
        switch ($estadoString) {
            case 'Aceptado': return 1;
            case 'Rechazado': return 2;
            case 'Pendiente':
            default: return 0;
        }
    }

    /**
     * Store a newly created postulation from the public user.
     * Almacena una nueva postulación enviada por el usuario público.
     */
    public function store(Request $request)
    {
        try {
            // <<<< MODIFICADO: Reglas de validación >>>>
            // Solo validar vacantesId, ya que estado y fecha son automáticos.
            $validator = Validator::make($request->all(), [
                'vacantesId' => 'required|integer|exists:vacantes,idVacantes',
                // Si envías userId del frontend, valida también:
                // 'userId' => 'required|integer|exists:users,id',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'message' => 'Errores de validación',
                    'errors' => $validator->errors(),
                ], 422);
            }

            // Obtén los datos validados (principalmente vacantesId)
            $validatedData = $validator->validated();

            // <<<< MODIFICADO: Creación de la postulación asignando valores automáticos >>>>
            $postulacion = new Postulaciones(); // Crea una nueva instancia del modelo

            $postulacion->vacantesId = $validatedData['vacantesId']; // Asigna el ID de la vacante recibido
            $postulacion->fechaPostulacion = Carbon::now()->toDateString(); // Asigna la fecha actual usando Carbon
            $postulacion->estado = $this->mapEstadoToTinyint('Pendiente'); // Asigna el estado 'Pendiente' (mapeado a Tinyint)

            // Si manejas userId y lo validaste:
            // $postulacion->userId = $validatedData['userId'];

            $postulacion->save(); // Guarda el registro en la base de datos


            // Retorna una respuesta de éxito
            return response()->json([
                'message' => 'Postulación creada exitosamente',
                'data' => $postulacion, // Opcional: devuelve la postulación creada
            ], 201);

        } catch (\Exception $e) {
            Log::error('Error al crear postulación (PostulacionesController::store): ' . $e->getMessage());
            return response()->json([
                'message' => 'Ocurrió un error al crear la postulación.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}