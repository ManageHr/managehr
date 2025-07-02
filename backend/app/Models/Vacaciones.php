<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Carbon\Carbon;

class Vacaciones extends Model
{
    use HasFactory;

    protected $table = 'vacaciones';
    protected $primaryKey = 'idVacaciones';

    public $timestamps = false;

    protected $fillable = [
        'motivo',
        'fechaInicio',
        'fechaFinal',
        'contratoId',
        'estado',

    ];

    protected $casts = [
        'estado'      => 'string',
        'fechaInicio' => 'date:Y-m-d',
        'fechaFinal'  => 'date:Y-m-d',
    ];


    public function getDiasAttribute(): int
    {
        $inicio = Carbon::parse($this->fechaInicio);
        $fin    = Carbon::parse($this->fechaFinal);


        return $inicio->diffInDays($fin) + 1;
    }
    public function contrato()
    {
        return $this->belongsTo(Contrato::class, 'contratoId', 'idContrato');
    }
    public function area()
    {
        return $this->belongsTo(Area::class, 'idArea', 'idArea');
    }
    public function usuario()
    {
        return $this->belongsTo(User::class, 'usersId', 'id'); // ajusta campos
    }
    public function tipoContrato()
    {
        return $this->belongsTo(TipoContrato::class, 'idTipoContrato', 'idTipoContrato');
    }
}
