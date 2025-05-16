<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Postulaciones extends Model
{
    use HasFactory;

    protected $table = 'postulaciones';
    protected $primaryKey = 'idPostulaciones';
    public $timestamps = false;

    protected $fillable = [
        'fechaPostulacion',
        'estado',
        'vacantesId',
        'usuarioId',
        'numdocumento',
    ];

    protected $casts = [
        'estado' => 'integer',
        'vacantesId' => 'integer',
        'usuarioId' => 'integer',
        'numdocumento' => 'string',
    ];

    protected $appends = ['fecha_formateada'];

     protected $with = ['vacante'];

    public function getFechaFormateadaAttribute()
    {
        if (!$this->fechaPostulacion) {
            return null;
        }

        return Carbon::parse($this->fechaPostulacion)->format('d/m/Y');
    }

    public function vacante()
    {
        return $this->belongsTo(\App\Models\Vacantes::class, 'vacantesId', 'idVacantes');
    }
}
