<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\Contrato;
use App\Models\Hojasvida;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

/**
 * @OA\Tag(
 *     name="Contratos",
 *     description="Gestión de los contratos de los empleados"
 * )
 */
class contratoController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/contrato",
     *     summary="Listar todos los contratos",
     *     tags={"Contratos"},
     *     security={{"bearerAuth":{}}}, 
     *     @OA\Response(
     *         response=200,
     *         description="Listado de contratos exitoso"
     *     )
     * )
     */
    public function index()
    {
        $contratos = Contrato::with([
            'hojaDeVida:idHojaDeVida,usuarioNumDocumento',
            'hojaDeVida.usuario:numDocumento,primerNombre,primerApellido',
            'area:idArea,nombreArea',
            'tipoContrato:idTipoContrato,nomTipoContrato'
        ])->get([
            'idContrato',
            'tipoContratoId',
            'hojaDeVida',
            'area',
            'fechaIngreso',
            'fechaFinalizacion',
            'archivo',
            'estado',
            'cargoArea', // agregar aquí
        ]);

        return response()->json([
            'contratos' => $contratos,
            'status' => 200
        ]);
    }



    /**
     * @OA\Post(
     *     path="/api/contrato",
     *     summary="Crear un nuevo contrato",
     *     tags={"Contratos"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"numDocumento", "tipoContratoId", "estado", "fechaIngreso", "fechaFinalizacion", "area", "cargoArea"},
     *             @OA\Property(property="numDocumento", type="integer"),
     *             @OA\Property(property="tipoContratoId", type="integer"),
     *             @OA\Property(property="estado", type="integer"),
     *             @OA\Property(property="fechaIngreso", type="string", format="date"),
     *             @OA\Property(property="fechaFinalizacion", type="string", format="date"),
     *             @OA\Property(property="archivo", type="string", format="binary"),
     *             @OA\Property(property="area", type="integer"),
     *             @OA\Property(property="cargoArea", type="integer", enum={1,2})
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Contrato creado correctamente"
     *     ),
     *     @OA\Response(response=400, description="Error de validación")
     * )
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'numDocumento' => 'required|integer',
            'tipoContratoId' => 'required|integer',
            'estado' => 'required|integer',
            'fechaIngreso' => 'required|date',
            'fechaFinalizacion' => 'required|date|after_or_equal:fechaIngreso',
            'archivo' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'area' => 'required|integer',
            'cargoArea' => 'required|integer|in:1,2',  // validamos cargoArea
        ]);

        $documento = $validated['numDocumento'];
        $hoja = Hojasvida::where('usuarioNumDocumento', $documento)->first();

        if (!$hoja) {
            return response()->json([
                'mensaje' => "Hoja de vida no encontrada para el documento $documento",
                'status' => 404
            ], 404);
        }

        if ($request->hasFile('archivo')) {
            $file = $request->file('archivo');
            $extension = $file->getClientOriginalExtension();
            $filename = time() . '_' . $documento . '.' . $extension;
            $folder = 'Archivos/' . $documento;
            $path = $file->storeAs($folder, $filename, 'public');
            $validated['archivo'] = 'storage/' . $path;
        }

        unset($validated['numDocumento']);
        $validated['hojaDeVida'] = $hoja->idHojaDeVida;


        $contrato = Contrato::create($validated);

        return response()->json([
            'mensaje' => 'Contrato creado correctamente',
            'contrato' => $contrato,
            'status' => 201
        ]);
    }

    /**
     * @OA\Get(
     *     path="/api/contrato/{id}",
     *     summary="Obtener detalles de un contrato",
     *     tags={"Contratos"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID del contrato",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(response=200, description="Contrato encontrado"),
     *     @OA\Response(response=404, description="Contrato no encontrado")
     * )
     */
    public function show($id)
    {
        $contrato = Contrato::with('hojaDeVida')->find($id);

        if (!$contrato) {
            return response()->json([
                'mensaje' => 'Contrato no encontrado',
                'status' => 404
            ], 404);
        }

        return response()->json([
            'contrato' => $contrato,
            'status' => 200
        ]);
    }
    /**
     * @OA\Delete(
     *     path="/api/contrato/{id}",
     *     summary="Eliminar un contrato",
     *     tags={"Contratos"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID del contrato a eliminar",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(response=200, description="Contrato eliminado correctamente"),
     *     @OA\Response(response=404, description="Contrato no encontrado")
     * )
     */
    public function destroy($id)
    {
        $contrato = Contrato::find($id);

        if (!$contrato) {
            return response()->json([
                "mensaje" => "No se encontró el contrato",
                "status" => 404
            ], 404);
        }

        $contrato->delete();

        return response()->json([
            "mensaje" => "Contrato eliminado correctamente",
            "status" => 200
        ]);
    }

    /**
     * @OA\Put(
     *     path="/api/contrato/{id}",
     *     summary="Actualizar un contrato",
     *     tags={"Contratos"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID del contrato",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="tipoContratoId", type="integer"),
     *             @OA\Property(property="estado", type="integer"),
     *             @OA\Property(property="fechaIngreso", type="string", format="date"),
     *             @OA\Property(property="fechaFinalizacion", type="string", format="date"),
     *             @OA\Property(property="archivo", type="string", format="binary"),
     *             @OA\Property(property="area", type="integer"),
     *             @OA\Property(property="cargoArea", type="integer", enum={1,2})
     *         )
     *     ),
     *     @OA\Response(response=200, description="Contrato actualizado correctamente"),
     *     @OA\Response(response=400, description="Error de validación")
     * )
     */
    public function update(Request $request, $id)
    {
        $contrato = Contrato::find($id);

        if (!$contrato) {
            return response()->json([
                "mensaje" => "No se encontró el contrato",
                "status" => 404
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'tipoContratoId' => 'required|integer',
            'estado' => 'required|integer',
            'fechaIngreso' => 'required|date',
            'fechaFinalizacion' => 'required|date|after_or_equal:fechaIngreso',
            'archivo' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'area' => 'required|integer',
            'cargoArea' => 'required|integer|in:1,2',  // validamos cargoArea
        ]);

        if ($validator->fails()) {
            return response()->json([
                "errors" => $validator->errors(),
                "status" => 400
            ], 400);
        }

        $contrato->estado = $request->estado;
        $contrato->fechaIngreso = $request->fechaIngreso;
        $contrato->fechaFinalizacion = $request->fechaFinalizacion;
        $contrato->tipoContratoId = $request->tipoContratoId;
        $contrato->area = $request->area;
        $contrato->cargoArea = $request->cargoArea;  // asignamos cargoArea

        if ($request->hasFile('archivo')) {
            $documento = $request->input('numDocumento');
            if (!$documento) {
                return response()->json([
                    'mensaje' => 'Se requiere numDocumento para subir archivo',
                    'status' => 400
                ], 400);
            }
            $hoja = Hojasvida::where('usuarioNumDocumento', $documento)->first();
            if (!$hoja) {
                return response()->json([
                    'mensaje' => 'Hoja de vida no encontrada para el documento ' . $documento,
                    'status' => 404
                ], 404);
            }

            $file = $request->file('archivo');
            $extension = $file->getClientOriginalExtension();
            $filename = $documento . '.' . $extension;
            $folder = 'Archivos/' . $documento;
            $path = $file->storeAs($folder, $filename, 'public');

            $contrato->archivo = 'storage/' . $path;
        }

        $contrato->save();

        return response()->json([
            "mensaje" => "Contrato actualizado correctamente",
            "contrato" => $contrato,
            "status" => 200
        ]);
    }


    /**
     * @OA\Patch(
     *     path="/api/contrato/updatePartial/{id}",
     *     summary="Actualizar parcialmente un contrato",
     *     tags={"Contratos"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID del contrato",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         @OA\JsonContent(
     *             @OA\Property(property="tipoContratoId", type="integer"),
     *             @OA\Property(property="estado", type="integer"),
     *             @OA\Property(property="fechaIngreso", type="string", format="date"),
     *             @OA\Property(property="fechaFinalizacion", type="string", format="date"),
     *             @OA\Property(property="archivo", type="string", format="binary"),
     *             @OA\Property(property="area", type="integer"),
     *             @OA\Property(property="cargoArea", type="integer", enum={1,2}),
     *             @OA\Property(property="numDocumento", type="integer", example=12345678)
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Contrato actualizado parcialmente"
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Error de validación o archivo faltante"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Contrato no encontrado"
     *     )
     * )
     */

    public function updatePartial(Request $request, $id)
    {
        $contrato = Contrato::find($id);

        if (!$contrato) {
            return response()->json([
                "mensaje" => "No se encontró el contrato",
                "status" => 404
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'tipoContratoId' => 'nullable|integer',
            'estado' => 'nullable|integer',
            'fechaIngreso' => 'nullable|date',
            'fechaFinalizacion' => 'nullable|date',
            'area' => 'nullable|integer',
            'cargoArea' => 'nullable|integer|in:1,2',  // validamos cargoArea opcional
            'archivo' => 'nullable|file|max:5120'
        ]);

        if ($validator->fails()) {
            return response()->json([
                "mensaje" => "Error al validar el contrato",
                "errors" => $validator->errors(),
                "status" => 400
            ], 400);
        }

        if ($request->filled('estado'))
            $contrato->estado = $request->estado;
        if ($request->filled('fechaIngreso'))
            $contrato->fechaIngreso = $request->fechaIngreso;
        if ($request->filled('fechaFinalizacion'))
            $contrato->fechaFinalizacion = $request->fechaFinalizacion;
        if ($request->filled('tipoContratoId'))
            $contrato->tipoContratoId = $request->tipoContratoId;
        if ($request->filled('area'))
            $contrato->area = $request->area;
        if ($request->filled('cargoArea'))
            $contrato->cargoArea = $request->cargoArea;

        if ($request->hasFile('archivo')) {
            $documento = $request->input('numDocumento');
            if (!$documento) {
                return response()->json([
                    'mensaje' => 'Se requiere numDocumento para subir archivo',
                    'status' => 400
                ], 400);
            }
            $hoja = Hojasvida::where('usuarioNumDocumento', $documento)->first();
            if (!$hoja) {
                return response()->json([
                    'mensaje' => 'Hoja de vida no encontrada para el documento ' . $documento,
                    'status' => 404
                ], 404);
            }

            $file = $request->file('archivo');
            $extension = $file->getClientOriginalExtension();
            $filename = $documento . '.' . $extension;
            $folder = 'Archivos/' . $documento;
            $path = $file->storeAs($folder, $filename, 'public');

            $contrato->archivo = 'storage/' . $path;
        }

        $contrato->save();

        return response()->json([
            "mensaje" => "Contrato actualizado correctamente",
            "contrato" => $contrato,
            "status" => 200
        ]);
    }
    /**
     * @OA\Get(
     *     path="/api/contrato/documento/{numDocumento}",
     *     summary="Buscar contrato por número de documento del usuario",
     *     tags={"Contratos"},
     *     @OA\Parameter(
     *         name="numDocumento",
     *         in="path",
     *         description="Número de documento del usuario",
     *         required=true,
     *         @OA\Schema(type="integer", example=12345678)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Contrato encontrado",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string"),
     *             @OA\Property(property="contrato", type="object")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Hoja de vida o contrato no encontrado"
     *     )
     * )
     */

    public function buscarPorDocumento($numDocumento)
    {
        $hoja = Hojasvida::where('usuarioNumDocumento', $numDocumento)->first();

        if (!$hoja) {
            return response()->json([
                'message' => 'Hoja de vida no encontrada',
                'contrato' => null
            ], 404);
        }

        $contrato = Contrato::with('hojaDeVida')
            ->where('hojaDeVida', $hoja->idHojaDeVida)
            ->first();

        if (!$contrato) {
            return response()->json([
                'message' => 'Contrato no encontrado',
                'contrato' => null
            ], 404);
        }

        return response()->json([
            'message' => 'Contrato encontrado',
            'contrato' => $contrato
        ], 200);
    }
    /**
     * @OA\Get(
     *     path="/api/contrato/area",
     *     summary="Obtener todos los contratos con detalle de área, tipo y usuario",
     *     tags={"Contratos"},
     *     @OA\Response(
     *         response=200,
     *         description="Listado de contratos con relaciones",
     *         @OA\JsonContent(
     *             @OA\Property(property="mensaje", type="string"),
     *             @OA\Property(property="data", type="array", @OA\Items(type="object"))
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Error interno al obtener contratos"
     *     )
     * )
     */

    public function obtenerContratosConArea()
    {
        try {
            $contratos = Contrato::with([
                'area',
                'tipoContrato',
                'hojaDeVida.usuario'
            ])->get();

            return response()->json([
                'mensaje' => 'Contratos obtenidos correctamente',
                'data' => $contratos
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'mensaje' => 'Error al obtener contratos',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
