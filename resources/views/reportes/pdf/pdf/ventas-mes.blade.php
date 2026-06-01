<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<style>
    * { margin:0; padding:0; box-sizing:border-box; }
    body { font-family: Arial, sans-serif; font-size: 11px; }
    .header { background: #EA4335; color: #fff; padding: 12px 16px; margin-bottom: 16px; }
    .header h1 { font-size: 18px; }
    .header p { font-size: 11px; opacity:.8; margin-top:2px; }
    .seccion { margin: 0 16px 16px; }
    .seccion h2 { font-size: 13px; margin-bottom:8px; border-bottom:2px solid #EA4335; padding-bottom:4px; }
    table { width:100%; border-collapse:collapse; margin-bottom:16px; }
    th { background:#EA4335; color:#fff; padding:7px; font-size:10px; text-align:left; }
    td { padding:6px 7px; font-size:10px; border-bottom:1px solid #eee; }
    .top-prod { background:#fff3cd; }
    .footer { margin-top:16px; text-align:center; font-size:9px; color:#aaa; padding:8px; border-top:1px solid #eee; }
</style>
</head>
<body>

<div class="header">
    <h1>📈 Ventas del Mes</h1>
    <p>{{ $inicio->locale('es')->isoFormat('MMMM YYYY') }} — {{ auth()->user()->tenant->nombre ?? '' }}</p>
</div>

<div class="seccion">
    <h2>Resumen</h2>
    <table>
        <tr>
            <td><strong>Total ventas:</strong></td>
            <td>{{ $ventas->count() }}</td>
            <td><strong>Total ingresos:</strong></td>
            <td><strong>$ {{ number_format($totalIngresos, 0, ',', '.') }}</strong></td>
        </tr>
    </table>
</div>

<div class="seccion">
    <h2>Top 10 productos más vendidos</h2>
    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Producto</th>
                <th style="text-align:center">Unidades</th>
                <th style="text-align:right">Ingresos</th>
            </tr>
        </thead>
        <tbody>
            @foreach($productosVendidos as $i => $p)
            <tr class="{{ $i < 3 ? 'top-prod' : '' }}">
                <td>{{ $i + 1 }}</td>
                <td>{{ $p->nombre_producto }}</td>
                <td style="text-align:center;font-weight:bold;">{{ $p->total_cantidad }}</td>
                <td style="text-align:right;">$ {{ number_format($p->total_ingresos, 0, ',', '.') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>

<div class="seccion">
    <h2>Detalle de ventas</h2>
    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Fecha</th>
                <th>Cliente</th>
                <th>Pago</th>
                <th style="text-align:right">Total</th>
            </tr>
        </thead>
        <tbody>
            @forelse($ventas as $venta)
            <tr>
                <td>{{ str_pad($venta->id, 6, '0', STR_PAD_LEFT) }}</td>
                <td>{{ $venta->created_at->format('d/m/Y') }}</td>
                <td>{{ $venta->cliente->nombre ?? 'Consumidor final' }}</td>
                <td>{{ ucfirst($venta->metodo_pago) }}</td>
                <td style="text-align:right;">$ {{ number_format($venta->total, 0, ',', '.') }}</td>
            </tr>
            @empty
            <tr><td colspan="5" style="text-align:center;color:#999;">Sin ventas</td></tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="footer">Sistema POS Ferretero — Avanzas Digital</div>
</body>
</html>