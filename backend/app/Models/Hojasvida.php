<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory; 


class Hojasvida extends Model
{
    use HasFactory;
    protected  $table = 'hojasvida';
    public $timestamps = false;
    protected $primaryKey = 'idHojaDeVida';
    protected $fillable = [
        'claseLibretaMilitar',
        'numeroLibretaMilitar',
        'usuarioNumDocumento'
    ];
    public function usuario()
{
    return $this->belongsTo(Usuarios::class, 'usuarioNumDocumento', 'numDocumento');
    // Ajusta si los campos se llaman diferente
}

}
