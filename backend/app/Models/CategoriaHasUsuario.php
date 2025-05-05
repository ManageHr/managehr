<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CategoriaHasUsuario extends Model
{
    use HasFactory;
    protected  $table = 'categoriavacanteshasusuario';
    public $timestamps = false;
    protected $primaryKey = 'usuarioNumDocumento';
    protected $fillable = [
        'categoriaVacantesId',
        'usuarioNumDocumento'
    ];
}
