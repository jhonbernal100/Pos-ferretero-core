<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use App\Models\Credito;
use Illuminate\Http\Request;

class ClienteController extends Controller
{
    public function index()
    {
        $clientes = Cliente::where('activo', true)
            ->orderBy('nombre')
            ->get();

        return view('clientes.index', compact('clientes'));
    }

    public function crear()
    {
        return view('clientes.crear');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:191',
        ]);

        $cliente = Cliente::create([
            'tenant_id'        => session('tenant_id'),
            'nombre'           => $request->nombre,
            'tipo_documento'   => $request->tipo_documento ?? 'CC',
            'numero_documento' => $request->numero_documento,
            'telefono'         => $request->telefono,
            'email'            => $request->email,
            'direccion'        => $request->direccion,
            'ciudad'           => $request->ciudad,
            'activo'           => true,
        ]);

        return response()->json([
            'success' => true,
            'mensaje' => 'Cliente creado correctamente',
            'cliente' => $cliente,
        ]);
    }

    public function creditos(Cliente $cliente)
    {
        $credito = Credito::where('cliente_id', $cliente->id)
            ->where('tenant_id', session('tenant_id'))
            ->first();

        return view('clientes.creditos', compact('cliente', 'credito'));
    }

    public function guardarCredito(Request $request, Cliente $cliente)
    {
        $request->validate([
            'tope_credito' => 'required|integer|min:0',
        ]);

        Credito::updateOrCreate(
            [
                'cliente_id' => $cliente->id,
                'tenant_id'  => session('tenant_id'),
            ],
            [
                'tope_credito' => $request->tope_credito,
                'estado'       => $request->estado ?? 'activo',
                'notas'        => $request->notas,
            ]
        );

        return response()->json([
            'success' => true,
            'mensaje' => 'Crédito actualizado correctamente',
        ]);
    }
}
