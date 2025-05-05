<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory; 

class Genero extends Model
{
    use HasFactory;
    protected  $table = 'genero';
    public $timestamps = false;
    protected $primaryKey = 'idGenero';
    protected $fillable = [
        'nombreGenero',
        'abreviacionGenero'
    ];
}
