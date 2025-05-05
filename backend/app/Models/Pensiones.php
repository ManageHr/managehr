<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Pensiones extends Model
{
    use HasFactory;

    protected $table = 'pensiones';
    public $timestamps = false;
    protected $primaryKey = 'codigoPensiones';

    protected $fillable = [
        'nombrePensiones'
    ];
}
