<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Usuarios extends Model
{
    use HasFactory;

    protected $table = 'usuarios';
    public $timestamps = false;
    protected $primaryKey = 'numDocumento';

    protected $fillable = [
        'numDocumento',
        'primerNombre',
        'segundoNombre',
        'primerApellido',
        'segundoApellido',
        'password',
        'fechaNac',
        'numHijos',
        'contactoEmergencia',
        'numContactoEmergencia',
        'email',
        'direccion',
        'telefono',
        'nacionalidadId',
        'epsCodigo',
        'generoId',
        'tipoDocumentoId',
        'estadoCivilId',
        'pensionesCodigo',
        'usersId'
    ];

    // ✅ Relaciones con otras tablas
    public function tipoDocumento()
    {
        return $this->belongsTo(TipoDocumento::class, 'tipoDocumentoId', 'idTipoDocumento');
    }

    public function genero()
    {
        return $this->belongsTo(Genero::class, 'generoId', 'idGenero');
    }
}
