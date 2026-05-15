<?php

namespace App\Http\Controllers;

use App\Models\Devolucion;
use App\Models\Venta;
use App\Models\VentaDetalle;
use App\Models\Producto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DevolucionController extends Controller
{
    public function index(Venta $venta)
    {
        $venta->load('detalles', 'cliente', 'tenant');

        $productos = Producto::where('activo', true)
            ->orderBy('nombre')
            ->get();

        return view('devoluciones.index', compact('venta', 'productos'));
    }

    public function procesar(Request $request, Venta $venta)
    {
        $request->validate([
            'tipo'             => 'required|in:devolucion_simple,cambio_producto,devolucion_parcial',
            'items_devueltos'  => 'required|array|min:1',
            'motivo'           => 'nullable|string|max:500',
        ]);

        DB::beginTransaction();

        try {
            $montoDevuelto = 0;
            $montoCobrado  = 0;
            $ventaNuevaId  = null;

            // Procesar items devueltos
            foreach ($request->items_devueltos as $item) {
                if (!isset($item['devolver']) || !$item['devolver']) continue;

                $detalle  = VentaDetalle::findOrFail($item['detalle_id']);
                $cantidad = (int) ($item['cantidad'] ?? $detalle->cantidad);

                if ($cantidad <= 0 || $cantidad > $detalle->cantidad) continue;

                // Calcular monto a devolver
                $subtotalDevuelto = ($detalle->precio_unitario * $cantidad);
                $montoDevuelto   += $subtotalDevuelto;

                // Devolver stock
                Producto::where('id', $detalle->producto_id)
                    ->increment('stock', $cantidad);
            }

            // Caso B — Cambio de producto
            if ($request->tipo === 'cambio_producto' && $request->items_nuevos) {
                $subtotalNuevo = 0;
                $detallesNuevos = [];

                foreach ($request->items_nuevos as $item) {
                    if (!$item['producto_id'] || !$item['cantidad']) continue;

                    $producto = Producto::findOrFail($item['producto_id']);

                    if ($producto->stock < $item['cantidad']) {
                        DB::rollBack();
                        return response()->json([
                            'success' => false,
                            'mensaje' => "Stock insuficiente para: {$producto->nombre}",
                        ], 422);
                    }

                    $subtotal       = $producto->precio_venta * $item['cantidad'];
                    $subtotalNuevo += $subtotal;

                    $detallesNuevos[] = [
                        'producto_id'     => $producto->id,
                        'nombre_producto' => $producto->nombre,
                        'cantidad'        => $item['cantidad'],
                        'precio_unitario' => $producto->precio_venta,
                        'subtotal'        => $subtotal,
                    ];

                    $producto->decrement('stock', $item['cantidad']);
                }

                $diferencia   = $subtotalNuevo - $montoDevuelto;
                $montoCobrado = max(0, $diferencia);

                // Crear nueva venta si hay productos nuevos
                if (count($detallesNuevos) > 0) {
                    $ventaNueva = Venta::create([
                        'tenant_id'      => session('tenant_id'),
                        'cliente_id'     => $venta->cliente_id,
                        'tipo_documento' => 'ticket',
                        'estado'         => 'completada',
                        'subtotal'       => $subtotalNuevo,
                        'descuento'      => 0,
                        'total'          => $subtotalNuevo,
                        'metodo_pago'    => $request->metodo_pago ?? 'efectivo',
                        'monto_pagado'   => $montoCobrado,
                        'cambio'         => 0,
                        'notas'          => 'Cambio de producto — venta original #' . str_pad($venta->id, 6, '0', STR_PAD_LEFT),
                    ]);

                    foreach ($detallesNuevos as $detalle) {
                        $detalle['venta_id'] = $ventaNueva->id;
                        VentaDetalle::create($detalle);
                    }

                    $ventaNuevaId = $ventaNueva->id;
                }
            }

            // Registrar devolución
            $devolucion = Devolucion::create([
                'tenant_id'     => session('tenant_id'),
                'venta_id'      => $venta->id,
                'venta_nueva_id'=> $ventaNuevaId,
                'tipo'          => $request->tipo,
                'monto_devuelto'=> $montoDevuelto,
                'monto_cobrado' => $montoCobrado,
                'motivo'        => $request->motivo,
                'estado'        => 'completada',
                'usuario_id'    => auth()->id(),
            ]);

            DB::commit();

            return response()->json([
                'success'        => true,
                'monto_devuelto' => $montoDevuelto,
                'monto_cobrado'  => $montoCobrado,
                'devolucion_id'  => $devolucion->id,
                'venta_nueva_id' => $ventaNuevaId,
                'mensaje'        => 'Devolución procesada correctamente',
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'mensaje' => $e->getMessage(),
            ], 500);
        }
    }
}
