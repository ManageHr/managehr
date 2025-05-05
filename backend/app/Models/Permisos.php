<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Permisos extends Model
{
    use HasFactory;

    // Nombre de la tabla
    protected  $table = 'permisos';

    // Indicamos que no usa timestamps
    public $timestamps = false;

    // Clave primaria, si es diferente a 'id' (opcional)
    protected $primaryKey = 'idPermiso';

    // Definir los campos que se pueden llenar en la base de datos
    protected $fillable = [
        "descrip",
        'fechaInicio',
        'estado',
        'tipoPermisoId',
        'fechaFinal',
        'contratoId'
    ];

    /**
     * Relación muchos a muchos con el modelo Rol.
     * Relacionamos la tabla pivote 'rol_permisos'
     * y los campos 'permiso_id' y 'rol_id'.
     */
    public function roles()
    {
        return $this->belongsToMany(Rol::class, 'rol_permisos', 'permiso_id', 'rol_id');
    }
}
