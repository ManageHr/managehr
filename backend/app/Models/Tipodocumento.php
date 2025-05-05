<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tipodocumento extends Model
{
    use HasFactory;
    protected  $table = 'tipodocumento';
    public $timestamps = false;
    protected $primaryKey = 'idTipDocumento';
    protected $fillable = [
        'nombreTipoDocumento'
    ];
}
