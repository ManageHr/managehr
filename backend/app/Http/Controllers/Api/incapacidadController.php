<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Incapacidad;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class incapacidadController extends Controller
{
    public function index(Request $request)
    {
        try {
            $incapacidades = Incapacidad::with([
                'contrato.hojaDeVida.usuario.user',
                'contrato.area'  // <-- Agregado para cargar área
            ])->get();

            return response()->json([
                'data' => $incapacidades
            ], 200);
        } catch (\Exception $e) {
            Log::error('Error al obtener incapacidades (IncapacidadController::index): ' . $e->getMessage());

            return response()->json([
                'message' => 'Ocurrió un error al obtener las incapacidades.',
                'error' => $e->getMessage()
            ], 500);
        }
    }



    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'descrip' => 'required|string|max:500',
            'archivo' => 'nullable|file|max:5120',
            'fechaInicio' => 'required|date',
            'fechaFinal' => 'required|date',
            'contratoId' => 'required|integer'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'mensaje' => 'Error en la validación de datos de la incapacidad',
                'errors' => $validator->errors(),
                'status' => 400
            ], 400);
        }
        if ($request->hasFile('archivo')) {
            $file = $request->file('archivo');
            $folder = 'Archivos/' . $request->input('contratoId');

            // crea carpeta si no existe, guarda archivo
            //$path = $file->storeAs($folder, $file->getClientOriginalName(), 'public');
            $extension = $file->getClientOriginalExtension();
            $filename = $request->input('contratoId') . '.' . $extension;
            $path = $file->storeAs($folder, $filename, 'public');

            // guardamos la URL relativa
            $validated['archivo'] = 'storage/' . $path;
        }
        try {
            $incapacidad = Incapacidad::create([
                'descrip' => $request->descrip,
                'archivo' => $request->archivo,
                'fechaInicio' => $request->fechaInicio,
                'fechaFinal' => $request->fechaFinal,
                'contratoId' => $request->contratoId

            ]);

            return response()->json([
                'mensaje' => 'incapacidad creado correctamente',
                'incapacidad' => $incapacidad,
                'status' => 201
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'mensaje' => 'Error al crear el incapacidad',
                'error' => $e->getMessage(),
                'status' => 500
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $incapacidad = Incapacidad::find($id);
        $data = [
            "incapacidad" => $incapacidad,
            "status" => 200
        ];
        return response()->json($data, 200);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function destroy($id)
    {
        $incapacidad = Incapacidad::find($id);
        if (!$incapacidad) {
            $data = [
                "mensage" => " No se encontro Incapacidad",
                "status" => 404
            ];
            return response()->json([$data], 404);
        }
        $incapacidad->delete();
        $data = [
            "incapacidad:" => 'incapacidad eliminado',
            "status" => 200
        ];
        return response()->json([$data], 200);
    }
    public function update(Request $request, $id)
    {
        $incapacidad = Incapacidad::find($id);
        if (!$incapacidad) {
            $data = [
                "mensage" => " No se encontro incapacidad",
                "status" => 404
            ];
            return response()->json([$data], 404);
        }
        $validator = Validator::make($request->all(), [

            'descrip' => 'required|string|max:500',
            'archivo' => 'nullable|file|max:5120',
            'fechaInicio' => 'required|date',
            'fechaFinal' => 'required|date',
            'contratoId' => 'required|integer'
        ]);
        if ($validator->fails()) {
            $data = [
                "errors" => $validator->errors(),
                "status" => 400
            ];
            return response()->json([$data], 400);
        }
        if ($request->hasFile('archivo')) {
            $file = $request->file('archivo');
            $folder = 'Archivos/' . $request->input('numDocumento');

            // crea carpeta si no existe, guarda archivo
            //$path = $file->storeAs($folder, $file->getClientOriginalName(), 'public');
            $extension = $file->getClientOriginalExtension();
            $filename = $request->input('numDocumento') . '.' . $extension;
            $path = $file->storeAs($folder, $filename, 'public');

            // guardamos la URL relativa
            $validated['archivo'] = 'storage/' . $path;
        }

        $incapacidad->descrip = $request->descrip;
        $incapacidad->archivo = $request->archivo;
        $incapacidad->fechaInicio = $request->fechaInicio;
        $incapacidad->fechaFinal = $request->fechaFinal;
        $incapacidad->contratoId = $request->contratoId;

        try {
            $incapacidad->save();
            $data = [
                "incapacidad" => $incapacidad,
                "status" => 200
            ];
            return response()->json([$data], 200);
        } catch (\Exception $e) {
            return response()->json([
                "mensaje" => "Error al modificar la incapacidad",
                "error" => $e->getMessage(),
                "status" => 500
            ], 500);
        }
    }
    public function updatePartial(Request $request, $id)
    {
        $incapacidad = Incapacidad::find($id);
        if (!$incapacidad) {
            $data = [
                "mensage" => " No se encontro incapacidad",
                "status" => 404
            ];
            return response()->json([$data], 404);
        }
        $validator = Validator::make($request->all(), [

            'descrip' => 'string|max:500',
            'archivo' => 'string|max:50',
            'fechaInicio' => 'date',
            'fechaFinal' => 'date',
            'contratoId' => 'integer',
        ]);
        if ($validator->fails()) {
            $data = [
                "mesaje " => "Error al validar incapacidad",
                "errors" => $validator->errors(),
                "status" => 400
            ];
            return response()->json([$data], 400);
        }
        if ($request->has("descrip")) {
            $incapacidad->descrip = $request->descrip;
        }
        if ($request->has("archivo")) {
            $incapacidad->archivo = $request->archivo;
        }
        if ($request->has("fechaInicio")) {
            $incapacidad->fechaInicio = $request->fechaInicio;
        }
        if ($request->has("fechaFinal")) {
            $incapacidad->fechaFinal = $request->fechaFinal;
        }
        if ($request->has("contratoId")) {
            $incapacidad->contratoId = $request->contratoId;
        }


        $incapacidad->save();
        $data = [
            "incapacidad:" => $incapacidad,
            "status" => 200
        ];
        return response()->json([$data], 200);
    }
}
