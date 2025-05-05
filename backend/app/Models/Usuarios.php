<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Usuarios extends Model
{
    use HasFactory;
    protected  $table = 'usuarios';
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
}
