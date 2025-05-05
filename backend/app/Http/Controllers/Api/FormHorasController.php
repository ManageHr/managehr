<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\HorasExtra; // Asegúrate de que el modelo esté definido correctamente

class FormHorasController extends Controller
{
    public function store(Request $request)
    {
        // Validación de datos
        $validatedData = $request->validate([
            'descrip' => 'required|string',
            'fecha' => 'required|date',
            'nHorasExtra' => 'required|integer|min:1',
            'tipohorasid' => 'required|integer|exists:tipos_horas,idTipoHoras',
            'contratoId' => 'required|integer',
        ]);

        // Guardar en la base de datos
        $horasExtra = HorasExtra::create($validatedData);

        // Respuesta JSON
        return response()->json([
            'message' => 'Solicitud de horas extra guardada con éxito.',
            'data' => $horasExtra
        ], 201);
    }
}   //

