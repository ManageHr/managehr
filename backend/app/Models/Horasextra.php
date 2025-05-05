<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Horasextra extends Model
{
    use HasFactory;

    protected $table = 'horasextra';
    protected $primaryKey = 'idHorasExtra';
    public $timestamps = false;

    protected $fillable = [
        'descrip',
        'fecha',
        'nHorasExtra',
        'tipoHorasid',
        'contratoId'
    ];
}
