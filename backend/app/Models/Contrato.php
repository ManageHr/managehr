<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Contrato extends Model
{
    use HasFactory;
    protected  $table = 'contrato';
    public $timestamps = false;
    protected $primaryKey = 'idContrato';
    protected $fillable = [
        "estado",
        'fechaIngreso',
        'fechaFinal',
        'documento',
        'tipoContratoId',
        'numDocumento',
        'areaId'
    ];
}
