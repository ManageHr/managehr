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

    /**
     * Relación: Un contrato pertenece a un área
     */
    public function area()
    {
        return $this->belongsTo(Area::class, 'area', 'idArea');
    }

    /**
     * Relación: Un contrato pertenece a un tipo de contrato
     */
    public function tipoContrato()
    {
        return $this->belongsTo(TipoContrato::class, 'tipoContratoId', 'idTipoContrato');
    }

    /**
     * Relación: Un contrato pertenece a un usuario a través de la hoja de vida
     */
    public function usuario()
    {
        return $this->hasOneThrough(
            Usuarios::class,
            Hojasvida::class,
            'idHojaDeVida', // Clave foránea en hojasvida
            'numDocumento', // Clave foránea en usuarios
            'hojaDeVida', // Clave local en contrato
            'usuarioNumDocumento' // Clave local en hojasvida
        );
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
