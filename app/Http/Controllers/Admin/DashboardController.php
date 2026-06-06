<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
use App\Models\User;
use App\Models\Venta;
use App\Models\TrialRequest;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        // Métricas generales
        $totalFerreterias    = Tenant::count();
        $ferreteriasTrial    = Tenant::where('subscription_status', 'trial')->count();
        $ferreteriasActivas  = Tenant::where('subscription_status', 'activa')->count();
        $ferreteriasVencidas = Tenant::where('subscription_status', 'vencida')->count();
        $totalUsuarios       = User::where('rol', '!=', 'superadmin')->count();

        // Trials próximos a vencer (menos de 7 días)
        $trialsProximosVencer = Tenant::where('subscription_status', 'trial')
            ->whereNotNull('trial_ends_at')
            ->where('trial_ends_at', '>=', now())
            ->where('trial_ends_at', '<=', now()->addDays(7))
            ->orderBy('trial_ends_at')
            ->get();

        // Ferreterías recientes
        $ferreteriasRecientes = Tenant::with('usuarios')
            ->orderByDesc('created_at')
            ->limit(5)
            ->get();

        // Solicitudes trial pendientes
        $trialsPendientes = TrialRequest::where('estado', 'pendiente')
            ->orderByDesc('created_at')
            ->limit(5)
            ->get();

        // Ventas totales del mes en toda la plataforma
        $ventasMes = Venta::whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->where('estado', 'completada')
            ->sum('total');

        // Ferreterías completas con detalles
        $ferreterias = Tenant::with('usuarios')
            ->orderByDesc('created_at')
            ->get()
            ->map(function ($tenant) {
                $tenant->dias_restantes = null;

                if ($tenant->subscription_status === 'trial' && $tenant->trial_ends_at) {
                    $tenant->dias_restantes = max(0, now()->diffInDays($tenant->trial_ends_at, false));
                } elseif ($tenant->subscription_status === 'activa' && $tenant->subscription_ends_at) {
                    $tenant->dias_restantes = max(0, now()->diffInDays($tenant->subscription_ends_at, false));
                }

                return $tenant;
            });

        return view('admin.dashboard', compact(
            'totalFerreterias', 'ferreteriasTrial', 'ferreteriasActivas',
            'ferreteriasVencidas', 'totalUsuarios', 'trialsProximosVencer',
            'ferreteriasRecientes', 'trialsPendientes', 'ventasMes', 'ferreterias'
        ));
    }

    public function ferreteria(Tenant $tenant)
    {
        $tenant->load('usuarios');

        $ventas = Venta::where('tenant_id', $tenant->id)
            ->where('estado', 'completada')
            ->orderByDesc('created_at')
            ->limit(10)
            ->get();

        $totalVentas    = Venta::where('tenant_id', $tenant->id)->where('estado', 'completada')->sum('total');
        $totalProductos = \App\Models\Producto::where('tenant_id', $tenant->id)->count();

        return view('admin.ferreteria', compact('tenant', 'ventas', 'totalVentas', 'totalProductos'));
    }

    public function ampliarTrial(Tenant $tenant)
    {
        $dias = request('dias', 15);

        if ($tenant->subscription_status === 'trial') {
            $tenant->update([
                'trial_ends_at' => Carbon::parse($tenant->trial_ends_at)->addDays($dias),
            ]);
        } else {
            $tenant->update([
                'subscription_status' => 'trial',
                'trial_ends_at'       => now()->addDays($dias),
            ]);
        }

        return response()->json([
            'success' => true,
            'mensaje' => "Trial ampliado por {$dias} dias",
        ]);
    }

    public function cambiarPlan(Tenant $tenant)
    {
        request()->validate([
            'plan'   => 'required|in:trial,activa,vencida,suspendida',
            'meses'  => 'nullable|integer|min:1',
        ]);

        $data = ['subscription_status' => request('plan')];

        if (request('plan') === 'activa' && request('meses')) {
            $data['subscription_ends_at'] = now()->addMonths(request('meses'));
        }

        $tenant->update($data);

        return response()->json([
            'success' => true,
            'mensaje' => 'Plan actualizado correctamente',
        ]);
    }

    public function eliminarFerreteria(Tenant $tenant)
    {
        // Eliminar usuarios del tenant
        User::where('tenant_id', $tenant->id)->delete();

        // Eliminar tenant
        $tenant->delete();

        return response()->json([
            'success' => true,
            'mensaje' => 'Ferreteria eliminada correctamente',
        ]);
    }
}