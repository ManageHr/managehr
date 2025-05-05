<?php

namespace App\Http\Controllers\Api; 

use App\Http\Controllers\Controller; 
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse; 
use App\Models\Vacantes;

class VacantesUserController extends Controller
{
    
    public function index(): JsonResponse
    {

        $vacantes = Vacantes::all(); 

    
        return response()->json($vacantes);
    }

   
    public function show(string $id): JsonResponse
    {
       
        $vacante = Vacantes::findOrFail($id); 
        

        
        return response()->json($vacante);
    }

}