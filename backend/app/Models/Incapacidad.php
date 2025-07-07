<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Incapacidad extends Model
{
    use HasFactory;
    protected  $table = 'incapacidad';
    public $timestamps = false;
    protected $primaryKey = 'idIncapacidad';
    protected $fillable = [
        "descrip",
        'archivo',
        'fechaInicio',
        'fechaFinal',
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

    public function contrato()
    {
        return $this->belongsTo(Contrato::class, 'contratoId','idContrato');
    }
}
