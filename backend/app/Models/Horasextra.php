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
    ];
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
