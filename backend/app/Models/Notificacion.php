<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
class Notificacion extends Model
{
    use HasFactory;
    protected  $table = 'notificaciones';
    public $timestamps = false;
    protected $primaryKey = 'idNotificaciones';
    protected $fillable = [
        'tipo',
        'accion',
        'fecha',
        'detalle',
        'usuarioId',
        'areaId',
        'referenciaId'
    ];
    public function usuario()
    {
        return $this->belongsTo(Usuarios::class, 'usuarioId', 'usersId');
    }

    public function area()
    {
        return $this->belongsTo(Area::class, 'areaId', 'idArea');
    }
}
