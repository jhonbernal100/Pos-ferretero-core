<?php

namespace App\Http\Controllers;

use App\Models\Producto;
use App\Models\Venta;
use App\Models\VentaDetalle;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ReporteController extends Controller
{
    public function index()
    {
        return view('reportes.index');
    }

    public function inventario()
    {
        $productos = Producto::where('activo', true)
            ->orderBy('categoria')
            ->orderBy('nombre')
            ->get();

        $totalProductos    = $productos->count();
        $totalValorCosto   = $productos->sum(fn($p) => $p->precio_compra * $p->stock);
        $totalValorVenta   = $productos->sum(fn($p) => $p->precio_venta * $p->stock);
        $productosAgotados = $productos->where('stock', 0)->count();
        $productosBajos    = $productos->filter(fn($p) => $p->stock > 0 && $p->stock <= $p->stock_minimo)->count();

        $pdf = Pdf::loadView('reportes.pdf.inventario', compact(
            'productos', 'totalProductos', 'totalValorCosto',
            'totalValorVenta', 'productosAgotados', 'productosBajos'
        ))->setPaper('letter', 'portrait');

        return $pdf->stream('inventario-' . now()->format('Y-m-d') . '.pdf');
    }

    public function stockBajo()
    {
        $productos = Producto::where('activo', true)
            ->where(function ($q) {
                $q->where('stock', 0)
                  ->orWhereColumn('stock', '<=', 'stock_minimo');
            })
            ->orderBy('stock')
            ->get();

        $pdf = Pdf::loadView('reportes.pdf.stock-bajo', compact('productos'))
            ->setPaper('letter', 'portrait');

        return $pdf->stream('stock-bajo-' . now()->format('Y-m-d') . '.pdf');
    }

    public function ventasDia()
    {
        $hoy = Carbon::today();

        $ventas = Venta::with('cliente', 'detalles')
            ->whereDate('created_at', $hoy)
            ->where('estado', 'completada')
            ->orderByDesc('created_at')
            ->get();

        $totalVentas        = $ventas->count();
        $totalIngresos      = $ventas->sum('total');
        $totalEfectivo      = $ventas->where('metodo_pago', 'efectivo')->sum('total');
        $totalCredito       = $ventas->where('metodo_pago', 'credito')->sum('total');
        $totalTransferencia = $ventas->where('metodo_pago', 'transferencia')->sum('total');

        $pdf = Pdf::loadView('reportes.pdf.ventas-dia', compact(
            'ventas', 'totalVentas', 'totalIngresos',
            'totalEfectivo', 'totalCredito', 'totalTransferencia', 'hoy'
        ))->setPaper('letter', 'portrait');

        return $pdf->stream('ventas-dia-' . $hoy->format('Y-m-d') . '.pdf');
    }

    public function ventasSemana()
{
    $inicio = Carbon::now()->startOfWeek();
    $fin    = Carbon::now()->endOfWeek();

    $ventas = Venta::with('cliente', 'detalles')
        ->whereBetween('created_at', [$inicio, $fin])
        ->where('estado', 'completada')
        ->orderByDesc('created_at')
        ->get();

    $totalIngresos      = $ventas->sum('total');
    $totalEfectivo      = $ventas->where('metodo_pago', 'efectivo')->sum('total');
    $totalTransferencia = $ventas->where('metodo_pago', 'transferencia')->sum('total');
    $totalCredito       = $ventas->where('metodo_pago', 'credito')->sum('total');

    $ventasPorDia = $ventas->groupBy(fn($v) => $v->created_at->format('Y-m-d'))
        ->map(fn($grupo) => $grupo->sum('total'));

    $pdf = Pdf::loadView('reportes.pdf.ventas-semana', compact(
        'ventas', 'totalIngresos', 'totalEfectivo',
        'totalTransferencia', 'totalCredito',
        'ventasPorDia', 'inicio', 'fin'
    ))->setPaper('letter', 'portrait');

    return $pdf->stream('ventas-semana-' . now()->format('Y-m-d') . '.pdf');
}

public function ventasMes(Request $request)
{
    $mes  = $request->mes ?? now()->month;
    $anio = $request->anio ?? now()->year;

    $inicio = Carbon::createFromDate($anio, $mes, 1)->startOfMonth();
    $fin    = Carbon::createFromDate($anio, $mes, 1)->endOfMonth();

    $ventas = Venta::with('cliente', 'detalles')
        ->whereBetween('created_at', [$inicio, $fin])
        ->where('estado', 'completada')
        ->orderByDesc('created_at')
        ->get();

    $totalIngresos      = $ventas->sum('total');
    $totalEfectivo      = $ventas->where('metodo_pago', 'efectivo')->sum('total');
    $totalTransferencia = $ventas->where('metodo_pago', 'transferencia')->sum('total');
    $totalCredito       = $ventas->where('metodo_pago', 'credito')->sum('total');

    $productosVendidos = VentaDetalle::whereHas('venta', fn($q) =>
        $q->whereBetween('created_at', [$inicio, $fin])
          ->where('estado', 'completada')
          ->where('tenant_id', session('tenant_id'))
    )
    ->selectRaw('nombre_producto, SUM(cantidad) as total_cantidad, SUM(subtotal) as total_ingresos')
    ->groupBy('nombre_producto')
    ->orderByDesc('total_cantidad')
    ->limit(10)
    ->get();

    $ventasPorDia = $ventas->groupBy(fn($v) => $v->created_at->format('d'))
        ->map(fn($grupo) => $grupo->sum('total'));

    $pdf = Pdf::loadView('reportes.pdf.ventas-mes', compact(
        'ventas', 'totalIngresos', 'totalEfectivo',
        'totalTransferencia', 'totalCredito',
        'productosVendidos', 'ventasPorDia', 'inicio', 'fin', 'mes', 'anio'
    ))->setPaper('letter', 'portrait');

    return $pdf->stream('ventas-mes-' . $inicio->format('Y-m') . '.pdf');
}
public function creditos()
{
    $creditos = \App\Models\Credito::with('cliente')
        ->where('saldo_usado', '>', 0)
        ->orderByDesc('saldo_usado')
        ->get();

    $totalCartera      = $creditos->sum('saldo_usado');
    $totalClientes     = $creditos->count();
    $creditosActivos   = $creditos->where('estado', 'activo')->count();
    $creditosBloqueados= $creditos->where('estado', 'bloqueado')->count();

    $pdf = Pdf::loadView('reportes.pdf.creditos', compact(
        'creditos', 'totalCartera', 'totalClientes',
        'creditosActivos', 'creditosBloqueados'
    ))->setPaper('letter', 'portrait');

    return $pdf->stream('cartera-creditos-' . now()->format('Y-m-d') . '.pdf');
}
    public function kardex(Request $request)
    {
        $request->validate([
            'producto_id' => 'required|exists:productos,id',
        ]);

        $producto = Producto::findOrFail($request->producto_id);

        // Movimientos de salida por ventas
        $salidas = VentaDetalle::with('venta')
            ->where('producto_id', $producto->id)
            ->whereHas('venta', fn($q) =>
                $q->where('estado', 'completada')
                  ->where('tenant_id', session('tenant_id'))
            )
            ->orderBy('created_at')
            ->get();

        // Totales
        $totalSalidas  = $salidas->sum('cantidad');
        $totalIngresos = $salidas->sum('subtotal');

        // Stock inicial estimado = stock actual + total vendido
        $stockInicial = $producto->stock + $totalSalidas;

        // Construir movimientos con saldo acumulado
        $movimientos = [];
        $saldoActual = $stockInicial;

        // Fila de saldo inicial
        $movimientos[] = [
            'fecha'       => $producto->created_at->format('d/m/Y'),
            'tipo'        => 'SALDO INICIAL',
            'documento'   => '-',
            'entrada'     => $stockInicial,
            'salida'      => 0,
            'saldo'       => $stockInicial,
            'costo_unit'  => $producto->precio_compra,
            'valor_total' => $stockInicial * $producto->precio_compra,
        ];

        // Agregar salidas por ventas
        foreach ($salidas as $s) {
            $saldoActual -= $s->cantidad;
            $movimientos[] = [
                'fecha'       => $s->created_at->format('d/m/Y H:i'),
                'tipo'        => 'VENTA',
                'documento'   => str_pad($s->venta_id, 6, '0', STR_PAD_LEFT),
                'entrada'     => 0,
                'salida'      => $s->cantidad,
                'saldo'       => $saldoActual,
                'costo_unit'  => $s->precio_unitario,
                'valor_total' => $s->subtotal,
            ];
        }

        // Limpiar nombre del archivo
        $nombreArchivo = preg_replace('/[^a-zA-Z0-9\-_]/', '_', $producto->nombre);

        $pdf = Pdf::loadView('reportes.pdf.kardex', compact(
            'producto', 'movimientos', 'stockInicial',
            'totalSalidas', 'totalIngresos'
        ))->setPaper('letter', 'portrait');

        return $pdf->stream('kardex-' . $nombreArchivo . '-' . now()->format('Y-m-d') . '.pdf');
    }
}