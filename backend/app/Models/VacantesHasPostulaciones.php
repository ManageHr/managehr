<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VacantesHasPostulaciones extends Model
{
    use HasFactory;
    protected  $table = 'vacanteshaspostulaciones';
    public $timestamps = false;
    // protected $primaryKey = 'usuarioNumDocumento';
    protected $fillable = [
        'vacantesid',
        'postulacionesid'
    ];
}
