<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CategoriaVacantes extends Model
{
    use HasFactory;
    protected  $table = 'categoriavacantes';
    public $timestamps = false;
    protected $primaryKey = 'idCatVac';
    protected $fillable = [
        'nomCategoria',
    ];
}
