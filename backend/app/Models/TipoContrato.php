<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TipoContrato extends Model
{
    use HasFactory;
    protected  $table = 'tipocontrato';
    public $timestamps = false;
    protected $primaryKey = 'idTipoContrato';
    protected $fillable = [
        'nomTipoContrato'
    ];
}
