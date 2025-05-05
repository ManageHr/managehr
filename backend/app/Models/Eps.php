<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Eps extends Model
{
    use HasFactory;
    protected  $table = 'eps';
    public $timestamps = false;
    //protected $primaryKey = 'codigoEps';
    protected $fillable = [
        "codigoEps",
        'nombreEps'
    ];
}
