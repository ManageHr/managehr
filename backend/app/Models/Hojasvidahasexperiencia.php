<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Hojasvidahasexperiencia extends Model
{
    use HasFactory;

    protected $table = 'hojasvidahasexperiencia';
    protected $primaryKey = 'idHasexperiencia';
    public $timestamps = false;

    protected $fillable = [
        'idHojaDevida',
        'idExperiencia',
        'estado',
        'archivo'
    ];

    public function hojaDeVida()
    {
        return $this->belongsTo(Hojasvida::class, 'idHojaDevida', 'idHojaDeVida');
    }

    public function experiencia()
    {
        return $this->belongsTo(ExperienciaLaboral::class, 'idExperiencia', 'idExperiencia');
    }

}
