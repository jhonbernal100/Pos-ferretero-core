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
    // Menu de reportes
    public function index()
    {
        return view('reportes.index');
    }

    // Reporte de inventario completo
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

    // Reporte de alertas stock bajo
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

    // Reporte de ventas del día
    public function ventasDia()
    {
        $hoy = Carbon::today();

        $ventas = Venta::with('cliente', 'detalles')
            ->whereDate('created_at', $hoy)
            ->where('estado', 'completada')
            ->orderByDesc('created_at')
            ->get();

        $totalVentas    = $ventas->count();
        $totalIngresos  = $ventas->sum('total');
        $totalEfectivo  = $ventas->where('metodo_pago', 'efectivo')->sum('total');
        $totalCredito   = $ventas->where('metodo_pago', 'credito')->sum('total');
        $totalTransferencia = $ventas->where('metodo_pago', 'transferencia')->sum('total');

        $pdf = Pdf::loadView('reportes.pdf.ventas-dia', compact(
            'ventas', 'totalVentas', 'totalIngresos',
            'totalEfectivo', 'totalCredito', 'totalTransferencia', 'hoy'
        ))->setPaper('letter', 'portrait');

        return $pdf->stream('ventas-dia-' . $hoy->format('Y-m-d') . '.pdf');
    }

    // Reporte de ventas de la semana
    public function ventasSemana()
    {
        $inicio = Carbon::now()->startOfWeek();
        $fin    = Carbon::now()->endOfWeek();

        $ventas = Venta::with('cliente', 'detalles')
            ->whereBetween('created_at', [$inicio, $fin])
            ->where('estado', 'completada')
            ->orderByDesc('created_at')
            ->get();

        $totalIngresos = $ventas->sum('total');

        // Ventas por día de la semana
        $ventasPorDia = $ventas->groupBy(fn($v) => $v->created_at->format('Y-m-d'))
            ->map(fn($grupo) => $grupo->sum('total'));

        $pdf = Pdf::loadView('reportes.pdf.ventas-semana', compact(
            'ventas', 'totalIngresos', 'ventasPorDia', 'inicio', 'fin'
        ))->setPaper('letter', 'portrait');

        return $pdf->stream('ventas-semana-' . now()->format('Y-m-d') . '.pdf');
    }

    // Reporte de ventas del mes
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

        $totalIngresos = $ventas->sum('total');

        // Productos más vendidos
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

        // Ventas por día
        $ventasPorDia = $ventas->groupBy(fn($v) => $v->created_at->format('d'))
            ->map(fn($grupo) => $grupo->sum('total'));

        $pdf = Pdf::loadView('reportes.pdf.ventas-mes', compact(
            'ventas', 'totalIngresos', 'productosVendidos',
            'ventasPorDia', 'inicio', 'fin', 'mes', 'anio'
        ))->setPaper('letter', 'portrait');

        return $pdf->stream('ventas-mes-' . $inicio->format('Y-m') . '.pdf');
    }

    // Kardex de un producto
    public function kardex(Request $request)
    {
        $request->validate([
            'producto_id' => 'required|exists:productos,id',
        ]);

        $producto = Producto::findOrFail($request->producto_id);

        // Movimientos de salida (ventas)
        $salidas = VentaDetalle::with('venta')
            ->where('producto_id', $producto->id)
            ->whereHas('venta', fn($q) =>
                $q->where('estado', 'completada')
                  ->where('tenant_id', session('tenant_id'))
            )
            ->orderBy('created_at')
            ->get();

        $pdf = Pdf::loadView('reportes.pdf.kardex', compact('producto', 'salidas'))
            ->setPaper('letter', 'portrait');

        return $pdf->stream('kardex-' . $producto->nombre . '-' . now()->format('Y-m-d') . '.pdf');
    }
}