<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CorsMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next)
    {
        // Permitir solicitudes de cualquier origen
        // Modifica esta línea si deseas restringir los orígenes
        return $next($request)
            ->header('Access-Control-Allow-Origin', '*')  // Permitir todos los orígenes
            ->header('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS')  // Métodos permitidos
            ->header('Access-Control-Allow-Headers', 'Content-Type, X-Requested-With, Authorization, X-CSRF-TOKEN')  // Encabezados permitidos
            ->header('Access-Control-Allow-Credentials', 'true'); // Permite el envío de cookies, si es necesario
    }
}
