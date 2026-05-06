<?php

namespace App\Http\Controllers;

use App\Models\Proveedor;
use Illuminate\Http\Request;

class ProveedorController extends Controller
{
    public function index()
    {
        $proveedores = Proveedor::where('activo', true)
            ->orderBy('nombre')
            ->get();

        return view('proveedores.index', compact('proveedores'));
    }

    public function crear()
    {
        return view('proveedores.crear');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:191',
        ]);

        Proveedor::create([
            'tenant_id'  => session('tenant_id'),
            'nombre'     => $request->nombre,
            'nit'        => $request->nit,
            'contacto'   => $request->contacto,
            'telefono'   => $request->telefono,
            'email'      => $request->email,
            'ciudad'     => $request->ciudad,
            'notas'      => $request->notas,
            'activo'     => true,
        ]);

        return response()->json(['success' => true, 'mensaje' => 'Proveedor creado correctamente']);
    }
}
