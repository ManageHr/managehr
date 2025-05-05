<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Vacaciones extends Model
{
    use HasFactory;
    protected $table = 'vacaciones';
    public $timestamps = false;
    protected $primaryKey = 'idVacaciones';

    protected $fillable = [
        'motivo',
        'fechaInicio',
        'fechaFinal',
        'dias',
        'estado',
        'contratoId',
    ];
}
