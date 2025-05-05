<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
class Estadocivil extends Model
{
    use HasFactory;

    protected $table = 'estadocivil';
    public $timestamps = false;
    protected $primaryKey = 'idEstadoCivil';

    protected $fillable = [
        'nombreEstado'
    ];
}
