<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tipodocumento extends Model
{
    use HasFactory;

    protected $table = 'tipodocumento';
    public $timestamps = false;
    protected $primaryKey = 'idTipoDocumento'; // corregido

    protected $fillable = [
        'nombreTipoDocumento',
        'abreviacionTipoDocumento' // agregado
    ];
}
