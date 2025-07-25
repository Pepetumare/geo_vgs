<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Http\Request;

class Authenticate extends Middleware
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     */
    protected function redirectTo(Request $request): ?string
    {
        // Si la solicitud no espera una respuesta JSON (es decir, es una visita normal del navegador),
        // la redirige a la página de inicio de sesión.
        return $request->expectsJson() ? null : route('login');
    }
}