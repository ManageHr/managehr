<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Notificacion extends Model
{
    use HasFactory;

    protected $table = 'notificaciones';
    protected $primaryKey = 'idNotificacion';
    public $timestamps = false;

    protected $fillable = [
        'tipo',
        'accion',
        'detalle',
        'estado',
        'fecha',
        'referenciaId',
        'contratoId',
    ];

    public function contrato()
    {
        return $this->belongsTo(Contrato::class, 'contratoId', 'idContrato');
    }

}
