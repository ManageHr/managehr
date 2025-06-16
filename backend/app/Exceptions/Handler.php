<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Auth\AuthenticationException;
use Throwable;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

class Handler extends ExceptionHandler
{
    /**
     * No reportar estas excepciones
     *
     * @var array<int, class-string<Throwable>>
     */
    protected $dontReport = [
        //
    ];

    /**
     * Personalizar el mensaje para cuando no hay autenticación (token faltante o inválido)
     */
    protected function unauthenticated($request, AuthenticationException $exception)
    {
        dd('Entró a unauthenticated');

        if ($request->expectsJson()) {
            return response()->json(['error' => 'No autenticado. Token requerido'], 401);
        }

        
    }


    /**
     * Registrar manejadores de excepciones
     */
    public function register(): void
    {
        $this->renderable(function (Throwable $e, $request) {
            if ($request->expectsJson() || $request->is('api/*')) {
                if ($e instanceof TokenInvalidException) {
                    return response()->json(['error' => 'Token inválido'], 401);
                }

                if ($e instanceof TokenExpiredException) {
                    return response()->json(['error' => 'Token expirado'], 401);
                }

                if ($e instanceof JWTException) {
                    return response()->json(['error' => 'Token requerido'], 401);
                }

                if ($e instanceof AuthenticationException) {
                    return response()->json(['error' => 'No autenticado. Token requerido'], 401);
                }
            }
        });
    }

    /**
     * Sobrescribir render para manejar AuthenticationException también
     */
    public function render($request, Throwable $exception)
    {
        if ($request->expectsJson() || $request->is('api/*')) {
            if ($exception instanceof AuthenticationException) {
                return response()->json(['error' => 'No autenticado. Token requerido'], 401);
            }
            if ($exception instanceof TokenInvalidException) {
                return response()->json(['error' => 'Token inválido'], 401);
            }
            if ($exception instanceof TokenExpiredException) {
                return response()->json(['error' => 'Token expirado'], 401);
            }
            if ($exception instanceof JWTException) {
                return response()->json(['error' => 'Token requerido'], 401);
            }
        }

        return parent::render($request, $exception);
    }
}
