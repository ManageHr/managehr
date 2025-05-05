<?php

namespace App\Http\Controllers\Api; // Asegúrate de que este namespace sea correcto

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Vacaciones; // Usa el modelo Vacaciones existente
use Illuminate\Support\Facades\Storage; // Para manejar almacenamiento de archivos
use Illuminate\Support\Facades\Validator; // Para validación
use Illuminate\Support\Facades\Log; // Opcional: para registrar errores
use Illuminate\Database\Eloquent\ModelNotFoundException; // Para errores de modelo (ej: exists)


class formvacationController extends Controller // Usa el nombre exacto del controlador que creaste
{
    /**
     * Store a newly created vacation request with file upload.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        // 1. Validación de los datos recibidos
        // Asegúrate de que las reglas de validación coincidan con los datos que esperas
        // desde el formulario Angular y con los tipos de datos de tu tabla de BD.
        // NOTA: La regla 'max:500' para descrip es un ejemplo, ajústala si tu TEXT soporta más o menos.
        // NOTA: La columna 'archivo' en tu DB es ahora VARCHAR(255).
        $validator = Validator::make($request->all(), [
            'descrip' => 'required|string|max:500',
            // 'archivo' es 'nullable|file'. Permite que no se suba archivo.
            // max:5000 es ~5MB. mimes: define tipos permitidos.
            'archivo' => 'nullable|file|max:5000|mimes:pdf,doc,docx,jpg,png,jpeg',
            'fechaInicio' => 'required|date_format:Y-m-d', // Espera formato 'YYYY-MM-DD'
            'fechaFinal' => 'required|date_format:Y-m-d|after_or_equal:fechaInicio', // Después o igual que fechaInicio
            // **** REGLA CORREGIDA PARA contratoId ****
            // Valida que contratoId sea un entero requerido y que exista en la tabla 'contrato', columna 'idContrato'
            'contratoId' => 'required|integer|exists:contrato,idContrato',
        ]);

        // Verifica si la validación falla
        if ($validator->fails()) {
             Log::error('Error de validación al guardar solicitud de vacaciones con archivo', ['errors' => $validator->errors()->toArray(), 'request' => $request->all()]);
             return response()->json([
                'message' => 'Error de validación en los datos de la solicitud.',
                'errors' => $validator->errors(),
             ], 422); // Código 422 Unprocessable Entity
        }

        // 2. Manejo de la subida del archivo
        // Inicializamos la ruta del archivo a una cadena vacía.
        // Esto es importante porque la columna 'archivo' en tu DB es VARCHAR(255) NOT NULL.
        // Si no se sube archivo, guardaremos una cadena vacía ('').
        $filePath = '';
        // Verifica si la solicitud contiene un archivo con el nombre 'archivo'
        if ($request->hasFile('archivo')) {
            $file = $request->file('archivo');

            // Intenta guardar el archivo
            try {
                // Guarda el archivo en la subcarpeta 'uploads/vacaciones' dentro del disco 'public'.
                // Laravel generará un nombre de archivo único (hash) automáticamente.
                // Esto devuelve la ruta relativa, ej: 'uploads/vacaciones/nombrehash.jpg'
                // Como la columna de DB ahora es VARCHAR(255), esta ruta debería caber.
                $path = $file->store('uploads/vacaciones', 'public');

                // **** LA VERIFICACIÓN DE LONGITUD > 45 HA SIDO ELIMINADA ****
                // Ya no es necesaria porque la base de datos soporta hasta 255 caracteres.
                // Si la ruta generada excede los 255 caracteres, la base de datos lanzará un error.

                // Si la ruta se guardó correctamente, la asignamos a $filePath
                $filePath = $path;
                Log::info('Archivo de vacaciones guardado con ruta válida', ['path' => $filePath, 'original_name' => $file->getClientOriginalName()]);

            } catch (\Exception $e) {
                // Si ocurre cualquier otro error al guardar el archivo (permisos, disco lleno, etc.)
                Log::error('Error al guardar el archivo de vacaciones', ['error' => $e->getMessage(), 'request_file' => $request->file('archivo') ? $request->file('archivo')->getClientOriginalName() : 'N/A', 'request_data' => $request->except('archivo')]);
                 return response()->json([
                    'message' => 'Error al guardar el archivo adjunto.',
                    'error' => $e->getMessage(),
                 ], 500); // Código 500 Internal Server Error
            }
        } else {
             // Si el archivo no es obligatorio y no se envió
             Log::info('No se adjuntó archivo a la solicitud de vacaciones.', ['request_data' => $request->all()]);
             // $filePath ya está inicializado a '', lo cual es correcto para VARCHAR NOT NULL si no hay archivo
        }


        // 3. Guardar los datos en la base de datos usando el modelo Vacaciones existente
        try {
             // Usa el método create() del modelo Vacaciones para insertar la nueva fila
             $vacacion = Vacaciones::create([
                'descrip' => $request->input('descrip'), // O puedes usar $request->descrip
                'archivo' => $filePath, // Guarda la ruta relativa del archivo guardado (o cadena vacía si no hubo archivo)
                'fechaInicio' => $request->input('fechaInicio'),
                'fechaFinal' => $request->input('fechaFinal'),
                'contratoId' => $request->input('contratoId'), // Usa el contratoId recibido
                // Las columnas 'created_at' y 'updated_at' no se gestionan por Eloquent
                // porque 'public $timestamps = false;' está en tu modelo Vacaciones.
             ]);

             // Usamos $vacacion->idVacaciones ya que es la clave primaria en tu tabla/modelo
             Log::info('Solicitud de vacaciones guardada en DB', ['id' => $vacacion->idVacaciones, 'contratoId' => $vacacion->contratoId]);

        } catch (ModelNotFoundException $e) {
             // Este catch es poco probable que se active si la validación `exists` pasa,
             // pero lo mantenemos como defensa en profundidad.
              Log::error('Error de Modelo (contratoId no encontrado?) al guardar solicitud de vacaciones', ['error' => $e->getMessage(), 'request' => $request->all()]);
               return response()->json([
                'message' => 'Error de base de datos: El contrato especificado no existe.',
                'error' => $e->getMessage(),
            ], 404); // O 422
        }
         catch (\Exception $e) {
            // Captura cualquier otro error de base de datos (ej: restricción de NOT NULL violada inesperadamente, error SQL, etc.)

            // Opcional: Si guardaste un archivo y la inserción en DB falla, puedes borrar el archivo.
            // Solo intentamos borrar si $filePath contiene una ruta (es decir, si se subió y guardó un archivo exitosamente antes del fallo de DB).
            if (!empty($filePath) && Storage::disk('public')->exists($filePath)) {
                 Storage::disk('public')->delete($filePath);
                 Log::warning('Archivo adjunto eliminado tras fallo en inserción DB', ['path' => $filePath]);
            }

            Log::error('Error al guardar solicitud de vacaciones en DB', ['error' => $e->getMessage(), 'request' => $request->all()]);
            return response()->json([
                'message' => 'Error al guardar la solicitud de vacaciones en la base de datos.',
                'error' => $e->getMessage(),
            ], 500); // Código 500 Internal Server Error
        }


        // 4. Devolver una respuesta de éxito
        // Devolvemos un código 201 (Created) indicando que el recurso fue creado.
        // Incluimos un mensaje y los datos de la solicitud guardada, incluyendo el ID generado.
        return response()->json([
            'message' => 'Solicitud de vacaciones guardada con éxito.',
            'data' => $vacacion // Devuelve el objeto Vacaciones recién creado por Eloquent
        ], 201); // Código 201 Created
    }

    // Puedes añadir otros métodos si este controlador necesita manejar otras operaciones CRUD
    // para solicitudes de vacaciones con archivos, o seguir usando el controlador de tu compañero.
}