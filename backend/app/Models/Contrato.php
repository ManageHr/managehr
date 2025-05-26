<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Contrato extends Model
{
    use HasFactory;

    protected $table = 'contrato';
    protected $primaryKey = 'idContrato';
    public $timestamps = false;

    protected $fillable = [
        'estado',
        'fechaIngreso',
        'fechaFinal',
        'archivo',
        'tipoContratoId',
        'hojaDeVida',     // <- este es el campo clave foránea en la tabla `contrato`
        'area'
    ];

    /**
     * Relación: Un contrato pertenece a una hoja de vida
     */
    public function hojaDeVida()
    {
        return $this->belongsTo(\App\Models\Hojasvida::class, 'hojaDeVida', 'idHojaDeVida');
    }
}
