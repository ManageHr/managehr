<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Pazysalvo extends Model
{
    use HasFactory;
    protected  $table = 'pazysalvo';
    public $timestamps = false;
    protected $primaryKey = 'idPazSalvo';
    protected $fillable = [
        "descrip",
        'firma',
        'contratoId'
    ];
}
