<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Carbon\Carbon;

class Vacaciones extends Model
{
    use HasFactory;

    protected $table = 'vacaciones';
    protected $primaryKey = 'idVacaciones';
    public $timestamps = false; // Cambiar a `true` si la BD tiene `created_at` y `updated_at`

    protected $fillable = [
        'motivo',
        'fechaInicio',
        'fechaFinal',
        'contratoId',
        'dias',
        'estado',
    ];

    protected $casts = [
        'estado' => 'string', // Asegura que Laravel maneje `estado` correctamente como string
    ];

    // Accesor para calcular automáticamente la cantidad de días entre `fechaInicio` y `fechaFinal`
    public function getDiasAttribute()
    {
        return Carbon::parse($this->fechaFinal)->diffInDays(Carbon::parse($this->fechaInicio));
    }
}
