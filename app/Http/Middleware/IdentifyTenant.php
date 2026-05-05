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
        if (auth()->check() && isset(auth()->user()->tenant_id)) {
            // Usuario autenticado — usar su tenant
            $tenantId = auth()->user()->tenant_id;
            session(['tenant_id' => $tenantId]);
            config(['app.current_tenant_id' => $tenantId]);

        } elseif (!session()->has('tenant_id')) {
            // Sin login — usar primer tenant activo (modo desarrollo/MVP)
            $tenant = Tenant::where('activo', true)->first();
            if ($tenant) {
                session(['tenant_id' => $tenant->id]);
                config(['app.current_tenant_id' => $tenant->id]);
            }
        } else {
            config(['app.current_tenant_id' => session('tenant_id')]);
        }

        return $next($request);
    }
}