<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Support\Facades\Hash;

class User extends Authenticatable implements JWTSubject
{
    use HasFactory, Notifiable;

    /**
     * Cargar automáticamente la relación con la tabla 'usuarios' al acceder al usuario.
     */
    protected $with = ['perfil'];

    /**
     * Campos que pueden ser asignados masivamente.
     */
    protected $fillable = [
        'name', 'email', 'password', 'rol', 'numdocumento',
    ];

    /**
     * Campos que deben permanecer ocultos en las respuestas JSON.
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * Obtener el identificador JWT del usuario.
     *
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey(); // Retorna el ID del usuario
    }

    /**
     * Obtener los reclamos personalizados para el JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return []; // Puedes agregar información extra aquí si lo necesitas en tu JWT
    }

    /**
     * Mutador para hashear automáticamente la contraseña antes de guardarla en la base de datos.
     */
    public function setPasswordAttribute($value)
    {
        // Solo hashear la contraseña si no está ya hasheada
        $this->attributes['password'] = Hash::needsRehash($value) ? Hash::make($value) : $value;
    }

    /**
     * Relación con el modelo Usuarios (perfil del usuario).
     */
    public function perfil()
    {
        return $this->hasOne(Usuarios::class, 'usersId', 'id');
    }
    public function rol()
    {
        return $this->belongsTo(Rol::class, 'rol'); // si el campo se llama 'rol' en la tabla users
    }

}
