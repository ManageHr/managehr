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
        'fechaFinalizacion',
        'archivo',
        'tipoContratoId',
        'hojaDeVida',
        'area',
        'cargoArea'
    ];

    /**
     * Relación: Un contrato pertenece a una hoja de vida
     */
    public function hojaDeVida()
    {
        return $this->belongsTo(Hojasvida::class, 'hojaDeVida', 'idHojaDeVida');
    }
    public function area()
    {
        return $this->belongsTo(Area::class, 'area');
    }
    public function tipoContrato()
    {
        return $this->belongsTo(TipoContrato::class, 'tipoContratoId', 'idTipoContrato');
    }
    public function getCargoAreaDescripcionAttribute()
    {
        $cargos = [
            1 => 'Empleado',
            2 => 'Jefe de área',
        ];

        return $cargos[$this->cargoArea] ?? 'Desconocido';
    }
}
