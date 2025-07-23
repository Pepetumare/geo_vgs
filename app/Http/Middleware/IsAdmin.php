<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class IsAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        //Si el usuario está identificado y su rol es 'Admin'
        if (Auth::check() && Auth::user()->role =='admin'){
            //Permite que la solicitud continue
            return $next($request);
        }

        //Si no es admin, redirige al dashboard normal.
        return redirect('/dashboard');
    }
}
