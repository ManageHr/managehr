<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Hojasvidahasestudios extends Model
{
    use HasFactory;
    protected $table = 'hojasvidahasestudios';
    protected $primaryKey = 'idHasestudios';
    public $timestamps = false;

    protected $fillable = [
        'idHojaDeVida',
        'idEstudios',
        'estado',
        'archivo'
    ];
    public function hojaDeVida()
    {
        return $this->belongsTo(Hojasvida::class, 'idHojaDeVida', 'idHojaDeVida');
    }
    public function estudio()
    {
        return $this->belongsTo(Estudios::class, 'idEstudios', 'idEstudios');
    }
}
