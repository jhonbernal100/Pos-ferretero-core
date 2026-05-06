<?php

namespace App\Http\Controllers;

use App\Models\Venta;
use App\Models\VentaDetalle;
use App\Models\Producto;
use App\Models\Cliente;
use App\Models\Credito;
use App\Jobs\EnviarFacturaAlegra;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class VentaController extends Controller
{
    public function index()
    {
        $ventas = Venta::with('cliente')
            ->orderByDesc('created_at')
            ->paginate(20);

        return view('ventas.index', compact('ventas'));
    }

    public function crear()
    {
        $productos = Producto::where('activo', true)
            ->where('stock', '>', 0)
            ->orderBy('nombre')
            ->get();

        $clientes = Cliente::where('activo', true)
            ->orderBy('nombre')
            ->get();

        return view('ventas.crear', compact('productos', 'clientes'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'items'               => 'required|array|min:1',
            'items.*.producto_id' => 'required|exists:productos,id',
            'items.*.cantidad'    => 'required|integer|min:1',
            'metodo_pago'         => 'required|in:efectivo,transferencia,credito',
            'monto_pagado'        => 'required|integer|min:0',
        ]);

        DB::beginTransaction();

        try {
            $subtotal = 0;
            $detalles = [];

            foreach ($request->items as $item) {
                $producto = Producto::findOrFail($item['producto_id']);

                if ($producto->stock < $item['cantidad']) {
                    return response()->json([
                        'success' => false,
                        'mensaje' => "Stock insuficiente para: {$producto->nombre}",
                    ], 422);
                }

                $itemSubtotal = $producto->precio_venta * $item['cantidad'];
                $subtotal    += $itemSubtotal;

                $detalles[] = [
                    'producto_id'     => $producto->id,
                    'nombre_producto' => $producto->nombre,
                    'cantidad'        => $item['cantidad'],
                    'precio_unitario' => $producto->precio_venta,
                    'subtotal'        => $itemSubtotal,
                ];

                $producto->decrement('stock', $item['cantidad']);
            }

            // Validar crédito si el método de pago es crédito
            if ($request->metodo_pago === 'credito') {
                if (!$request->cliente_id) {
                    DB::rollBack();
                    return response()->json([
                        'success' => false,
                        'mensaje' => 'Debes seleccionar un cliente para ventas a crédito',
                    ], 422);
                }

                $credito = Credito::where('cliente_id', $request->cliente_id)
                    ->where('tenant_id', session('tenant_id'))
                    ->first();

                if (!$credito) {
                    DB::rollBack();
                    return response()->json([
                        'success' => false,
                        'mensaje' => 'Este cliente no tiene línea de crédito configurada',
                    ], 422);
                }

                if (!$credito->puedeComprar($subtotal)) {
                    DB::rollBack();
                    return response()->json([
                        'success' => false,
                        'mensaje' => 'Crédito insuficiente. Disponible: $ ' .
                            number_format($credito->saldoDisponible(), 0, ',', '.'),
                    ], 422);
                }
            }

            $descuento   = $request->descuento ?? 0;
            $total       = $subtotal - $descuento;
            $montoPagado = $request->monto_pagado;
            $cambio      = max(0, $montoPagado - $total);

            $venta = Venta::create([
                'tenant_id'      => session('tenant_id'),
                'cliente_id'     => $request->cliente_id,
                'tipo_documento' => $request->tipo_documento ?? 'ticket',
                'estado'         => 'completada',
                'subtotal'       => $subtotal,
                'descuento'      => $descuento,
                'total'          => $total,
                'metodo_pago'    => $request->metodo_pago,
                'monto_pagado'   => $montoPagado,
                'cambio'         => $cambio,
                'notas'          => $request->notas,
            ]);

            foreach ($detalles as $detalle) {
                $detalle['venta_id'] = $venta->id;
                VentaDetalle::create($detalle);
            }

            // Actualizar saldo de crédito si aplica
            if ($request->metodo_pago === 'credito' && $request->cliente_id) {
                Credito::where('cliente_id', $request->cliente_id)
                    ->where('tenant_id', session('tenant_id'))
                    ->increment('saldo_usado', $total);
            }

            // Despachar factura electrónica si aplica
            if ($request->tipo_documento === 'factura_electronica') {
                EnviarFacturaAlegra::dispatch($venta)->delay(now()->addSeconds(5));
            }

            DB::commit();

            return response()->json([
                'success'  => true,
                'venta_id' => $venta->id,
                'cambio'   => $cambio,
                'total'    => $total,
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'mensaje' => $e->getMessage(),
            ], 500);
        }
    }

    public function ticket(Venta $venta)
    {
        $venta->load('detalles', 'cliente', 'tenant');
        return view('ventas.ticket', compact('venta'));
    }

    public function anular(Venta $venta)
    {
        if ($venta->estado === 'anulada') {
            return response()->json([
                'success' => false,
                'mensaje' => 'La venta ya está anulada',
            ], 422);
        }

        DB::beginTransaction();

        try {
            foreach ($venta->detalles as $detalle) {
                Producto::where('id', $detalle->producto_id)
                    ->increment('stock', $detalle->cantidad);
            }

            // Revertir crédito si aplica
            if ($venta->metodo_pago === 'credito' && $venta->cliente_id) {
                Credito::where('cliente_id', $venta->cliente_id)
                    ->where('tenant_id', $venta->tenant_id)
                    ->decrement('saldo_usado', $venta->total);
            }

            $venta->update(['estado' => 'anulada']);

            DB::commit();

            return response()->json(['success' => true]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'mensaje' => $e->getMessage(),
            ], 500);
        }
    }
}