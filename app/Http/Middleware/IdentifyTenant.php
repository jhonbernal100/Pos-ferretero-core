<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\Tenant;
use Symfony\Component\HttpFoundation\Response;

class IdentifyTenant
{
    public function handle(Request $request, Closure $next): Response
    {
        // Rutas del admin no requieren tenant
        if ($request->is('admin*')) {
            return $next($request);
        }

        if (auth()->check() && isset(auth()->user()->tenant_id)) {
            $tenant = Tenant::find(auth()->user()->tenant_id);
        } else {
            $tenant = Tenant::where('activo', true)->first();
        }

        if (!$tenant) {
            return response()->view('errors.sin-tenant', [], 403);
        }

        // Verificar suscripción activa
        if (!$tenant->tieneAcceso()) {
            // Actualizar estado si venció
            if ($tenant->subscription_status === 'trial' &&
                $tenant->trial_ends_at &&
                $tenant->trial_ends_at->isPast()) {
                $tenant->update(['subscription_status' => 'vencida']);
            }

            if ($tenant->subscription_status === 'activa' &&
                $tenant->subscription_ends_at &&
                $tenant->subscription_ends_at->isPast()) {
                $tenant->update(['subscription_status' => 'vencida']);
            }

            return response()->view('errors.suscripcion-vencida', compact('tenant'), 403);
        }

        session(['tenant_id' => $tenant->id]);
        config(['app.current_tenant_id' => $tenant->id]);

        return $next($request);
    }
}