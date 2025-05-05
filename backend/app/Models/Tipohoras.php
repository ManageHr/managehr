<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Tipohoras extends Model
{
    use HasFactory;
    protected  $table = 'tipohoras';
    public $timestamps = false;
    protected $primaryKey = 'idTipoHoras';
    protected $fillable = [
        'nombreTipoHoras'
    ];
}
