<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Tenant;

class FerreteriaController extends Controller
{
    public function perfil()
    {
        $tenant = auth()->user()->tenant;
        return view('ferreteria.perfil', compact('tenant'));
    }

    public function actualizarPerfil(Request $request)
    {
        $request->validate([
            'nombre'    => 'required|string|max:191',
            'telefono'  => 'nullable|string|max:20',
            'direccion' => 'nullable|string|max:191',
            'ciudad'    => 'nullable|string|max:100',
        ]);

        auth()->user()->tenant->update([
            'nombre'    => $request->nombre,
            'telefono'  => $request->telefono,
            'direccion' => $request->direccion,
            'ciudad'    => $request->ciudad,
        ]);

        return response()->json([
            'success' => true,
            'mensaje' => 'Datos actualizados correctamente',
        ]);
    }

    public function suscripcion()
    {
        $tenant = auth()->user()->tenant;
        return view('ferreteria.suscripcion', compact('tenant'));
    }
}