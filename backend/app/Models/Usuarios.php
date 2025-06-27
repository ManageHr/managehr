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
    public $incrementing = false; 
    protected $keyType = 'integer';
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

    
    public function tipoDocumento()
    {
        return $this->belongsTo(TipoDocumento::class, 'tipoDocumentoId', 'idTipoDocumento');
    }

    public function genero()
    {
        return $this->belongsTo(Genero::class, 'generoId', 'idGenero');
    }
    public function estadoCivil()
    {
        return $this->belongsTo(EstadoCivil::class, 'estadoCivilId', 'idEstadoCivil');
    }

    public function eps()
    {
        return $this->belongsTo(Eps::class, 'epsCodigo', 'codigoEps');
    }

    public function pensiones()
    {
        return $this->belongsTo(Pensiones::class, 'pensionesCodigo', 'codigoPensiones');
    }

    public function nacionalidad()
    {
        return $this->belongsTo(Nacionalidad::class, 'nacionalidadId', 'idNacionalidad');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'usersId', 'id');
    }
}
