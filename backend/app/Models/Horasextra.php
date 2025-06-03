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
}
