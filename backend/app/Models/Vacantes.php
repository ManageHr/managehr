<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Vacantes extends Model
{
    use HasFactory;

    protected $table = 'vacantes';

    protected $primaryKey = 'idVacantes';
    public $incrementing = true;
    protected $keyType = 'int';

    public $timestamps = false; 

    protected $fillable = [
        'nomVacante',
        'descripVacante',
        'salario',
        'expMinima',
        'cargoVacante',
        'catVacId',
    ];
}