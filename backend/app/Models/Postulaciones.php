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
        'numdocumento', // <- agregado aquí
    ];

    protected $casts = [
        'estado' => 'integer',
        'vacantesId' => 'integer',
        'usuarioId' => 'integer',
        'numdocumento' => 'string', // <- agregado y asumido como texto por seguridad
    ];

    // Esto hace que el atributo aparezca en el JSON
    protected $appends = ['fecha_formateada'];

    public function getFechaFormateadaAttribute()
    {
        if (!$this->fechaPostulacion) {
            return null;
        }

        return Carbon::parse($this->fechaPostulacion)->format('d/m/Y');
    }
}
