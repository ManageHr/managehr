<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExperienciaLaboral extends Model
{
    use HasFactory;
    protected  $table = 'experiencialaboral';
    public $timestamps = false;
    protected $primaryKey = 'idExperiencia';
    protected $fillable = [
        "nomEmpresa",
        'nombJefe',
        'telefono',
        'cargo',
        'actividades',
        'certificado',
        'fechaInicio',
        'fechaFin',
    ];
}
