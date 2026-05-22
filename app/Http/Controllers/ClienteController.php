<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use App\Models\Credito;
use App\Models\Venta;
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

    public function historialCredito(Cliente $cliente)
    {
        $credito = Credito::where('cliente_id', $cliente->id)
            ->where('tenant_id', session('tenant_id'))
            ->first();

        $ventas = Venta::with('detalles')
            ->where('cliente_id', $cliente->id)
            ->where('metodo_pago', 'credito')
            ->where('estado', 'completada')
            ->orderByDesc('created_at')
            ->get();

        return view('clientes.historial', compact('cliente', 'credito', 'ventas'));
    }

    public function pagarCredito(Request $request, Cliente $cliente)
    {
        $request->validate([
            'monto_pagado' => 'required|integer|min:1',
        ]);

        $credito = Credito::where('cliente_id', $cliente->id)
            ->where('tenant_id', session('tenant_id'))
            ->firstOrFail();

        $montoPagado = $request->monto_pagado;

        if ($montoPagado > $credito->saldo_usado) {
            return response()->json([
                'success' => false,
                'mensaje' => 'El monto pagado no puede ser mayor al saldo usado',
            ], 422);
        }

        $abono = \App\Models\Venta::create([
            'tenant_id'      => session('tenant_id'),
            'cliente_id'     => $cliente->id,
            'tipo_documento' => 'abono_credito',
            'estado'         => 'completada',
            'subtotal'       => $montoPagado,
            'descuento'      => 0,
            'total'          => $montoPagado,
            'metodo_pago'    => $request->metodo_pago ?? 'efectivo',
            'monto_pagado'   => $montoPagado,
            'cambio'         => 0,
            'credito_pagado' => true,
            'notas'          => 'Abono a crédito — ' . $cliente->nombre,
        ]);

        $credito->decrement('saldo_usado', $montoPagado);

        if ($credito->fresh()->saldo_usado <= 0) {
            $credito->update([
                'estado'      => 'pagado',
                'saldo_usado' => 0,
            ]);

            Venta::where('cliente_id', $cliente->id)
                ->where('tenant_id', session('tenant_id'))
                ->where('metodo_pago', 'credito')
                ->where('credito_pagado', false)
                ->update(['credito_pagado' => true]);
        }

        return response()->json([
            'success'        => true,
            'mensaje'        => 'Pago registrado correctamente',
            'abono_id'       => $abono->id,
            'saldo_restante' => $credito->fresh()->saldo_usado,
        ]);
    }

    public function editar(Cliente $cliente)
    {
        return view('clientes.editar', compact('cliente'));
    }

    public function actualizar(Request $request, Cliente $cliente)
    {
        $request->validate([
            'nombre' => 'required|string|max:191',
        ]);

        $cliente->update([
            'nombre'           => $request->nombre,
            'tipo_documento'   => $request->tipo_documento ?? 'CC',
            'numero_documento' => $request->numero_documento,
            'telefono'         => $request->telefono,
            'email'            => $request->email,
            'direccion'        => $request->direccion,
            'ciudad'           => $request->ciudad,
        ]);

        return response()->json([
            'success' => true,
            'mensaje' => 'Cliente actualizado correctamente',
        ]);
    }
}