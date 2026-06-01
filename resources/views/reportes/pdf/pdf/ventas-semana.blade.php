<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<style>
    * { margin:0; padding:0; box-sizing:border-box; }
    body { font-family: Arial, sans-serif; font-size: 11px; }
    .header { background: #34A853; color: #fff; padding: 12px 16px; margin-bottom: 16px; }
    .header h1 { font-size: 18px; }
    .header p { font-size: 11px; opacity:.8; margin-top:2px; }
    .resumen { margin: 0 16px 16px; border:1px solid #ddd; border-radius:6px; padding:12px; }
    table { width:calc(100% - 32px); margin:0 16px; border-collapse:collapse; }
    th { background:#34A853; color:#fff; padding:8px; font-size:10px; text-align:left; }
    td { padding:6px 8px; font-size:10px; border-bottom:1px solid #eee; }
    .dia-row { background:#f5f5f5; font-weight:bold; }
    .footer { margin-top:16px; text-align:center; font-size:9px; color:#aaa; padding:8px; border-top:1px solid #eee; }
</style>
</head>
<body>

<div class="header">
    <h1>📅 Ventas de la Semana</h1>
    <p>{{ $inicio->format('d/m/Y') }} al {{ $fin->format('d/m/Y') }} — {{ auth()->user()->tenant->nombre ?? '' }}</p>
</div>

<div class="resumen">
    <strong>Total ingresos semana: $ {{ number_format($totalIngresos, 0, ',', '.') }}</strong>
    <br><br>
    <strong>Ventas por día:</strong><br>
    @foreach($ventasPorDia as $dia => $total)
    {{ \Carbon\Carbon::parse($dia)->locale('es')->isoFormat('dddd D/MM') }}: $ {{ number_format($total, 0, ',', '.') }}<br>
    @endforeach
</div>

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
            <td>{{ $venta->created_at->format('d/m/Y H:i') }}</td>
            <td>{{ $venta->cliente->nombre ?? 'Consumidor final' }}</td>
            <td>{{ ucfirst($venta->metodo_pago) }}</td>
            <td style="text-align:right;font-weight:bold;">$ {{ number_format($venta->total, 0, ',', '.') }}</td>
        </tr>
        @empty
        <tr><td colspan="5" style="text-align:center;padding:20px;color:#999;">No hay ventas esta semana</td></tr>
        @endforelse
    </tbody>
</table>

<div class="footer">Sistema POS Ferretero — Avanzas Digital</div>
</body>
</html>