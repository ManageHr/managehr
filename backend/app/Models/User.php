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
     * Campos que pueden ser asignados masivamente.
     */
    protected $fillable = [
        'name', 'email', 'password', 'rol',
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
}
