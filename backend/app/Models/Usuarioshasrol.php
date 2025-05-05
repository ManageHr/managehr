<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Usuarioshasrol extends Model
{
    use HasFactory;
    protected  $table = 'usuarioshasrol';
    public $timestamps = false;
    protected $primaryKey = 'usuarioNumDocumento';
    protected $fillable = [
        'estado',
        'usuarioNumDocumento',
        'rolId'
    ];
}
