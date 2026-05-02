<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class IdentifyTenant
{
    /**
     * Maneja la solicitud entrante y establece el contexto del cliente (Ferretería).
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // 1. Verificamos si el usuario ya inició sesión
        if (auth()->check()) {
            /**
             * 2. Extraemos el tenant_id del usuario autenticado.
             * Esto asegura que todas las consultas SQL posteriores se filtren
             * por la ferretería a la que pertenece el usuario.
             */
            $tenantId = auth()->user()->tenant_id;

            // 3. Guardamos el ID en la configuración dinámica de Laravel
            config(['app.current_tenant_id' => $tenantId]);
        }

        return $next($request);
    }
}