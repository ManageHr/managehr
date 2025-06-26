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
    public function idHojaDeVida()
    {
        return $this->belongsTo(Hojasvida::class, 'idHojaDeVida', 'idHojaDeVida');
    }
    public function idExperiencia()
    {
        return $this->belongsTo(ExperienciaLaboral::class, 'idExperiencia', 'idExperiencia');
    }
}
