<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Trazabilidad extends Model
{
    use HasFactory;
    protected  $table = 'trazabilidad';
    public $timestamps = false;
    protected $primaryKey = 'idTrazabilidad';
    protected $fillable = [
        "fechaModificacion",
        'ip',
        'usuarioanterior',
        'usuarionuevo',
        'claveAnterior',
        'claveNueva',
        'numDocumento'
    ];
}
