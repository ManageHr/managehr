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
        'contratoId'
    ];
}
