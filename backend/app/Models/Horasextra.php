<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HorasExtra extends Model
{
    protected $table = 'horasextra';
    protected $primaryKey = 'idHorasExtra';
    public $timestamps = false;

    protected $fillable = [
        'descripcion',
        'fecha',
        'tipoHorasId',
        'nHorasExtra',
        'contratoId',
        'estado',
    ];

    // Conversión de estado numérico <-> texto
    public static $estados = [
        0 => 'Pendiente',
        1 => 'Aprobado',
        2 => 'Rechazado',
    ];

    public function getEstadoAttribute($value)
    {
        return self::$estados[$value] ?? 'Pendiente';
    }

    public function setEstadoAttribute($value)
    {
        $map = array_flip(self::$estados);
        $this->attributes['estado'] = $map[ucfirst(strtolower($value))] ?? 0;
    }

    public function tipoHoraExtra()
    {
        return $this->belongsTo(TipoHoras::class, 'tipoHorasId', 'idTipoHoras');
    }
    public function contrato()
    {
        return $this->belongsTo(Contrato::class, 'contratoId')->with([
            'hojaDeVida.usuario.user.rol',
            'area',
        ]);
    }
}
