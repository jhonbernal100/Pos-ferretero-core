<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<style>
    * { margin:0; padding:0; box-sizing:border-box; }
    body { font-family: Arial, sans-serif; font-size: 11px; }
    .header { background: #000; color: #fff; padding: 12px 16px; margin-bottom: 16px; }
    .resumen { display:flex; gap:12px; margin: 0 16px 16px; }
    .card { flex:1; border:1px solid #ddd; border-radius:6px; padding:10px; text-align:center; }
    .card .valor { font-size:16px; font-weight:bold; margin-top:4px; }
    .card .label { font-size:10px; color:#888; }
    table { width:calc(100% - 32px); margin:0 16px; border-collapse:collapse; }
    th { background:#000; color:#fff; padding:8px; font-size:10px; text-align:left; }
    td { padding:6px 8px; font-size:10px; border-bottom:1px solid #eee; }
    .footer { margin-top:16px; text-align:center; font-size:9px; color:#aaa; padding:8px; border-top:1px solid #eee; }
</style>
</head>
<body>

<div class="header">
    <table style="width:100%;border-collapse:collapse;">
        <tr>
            <td style="width:70%;vertical-align:middle;">
                <h1 style="font-size:20px;color:#fff;margin-bottom:4px;">
                    {{ auth()->user()->tenant->nombre ?? 'Ferreteria' }}
                </h1>
                <p style="font-size:11px;opacity:.8;">
                    NIT: {{ auth()->user()->tenant->nit ?? '' }} |
                    Tel: {{ auth()->user()->tenant->telefono ?? '' }}
                </p>
                <p style="font-size:11px;opacity:.8;">
                    {{ auth()->user()->tenant->direccion ?? '' }} -
                    {{ auth()->user()->tenant->ciudad ?? '' }}
                </p>
            </td>
            <td style="width:30%;text-align:right;vertical-align:middle;">
                <p style="font-size:13px;font-weight:bold;color:#fff;">VENTAS DEL DIA</p>
                <p style="font-size:10px;opacity:.8;">{{ $hoy->format('d/m/Y') }}</p>
            </td>
        </tr>
    </table>
</div>

<div class="resumen">
    <div class="card">
        <div class="label">Total ventas</div>
        <div class="valor">{{ $totalVentas }}</div>
    </div>
    <div class="card">
        <div class="label">Total ingresos</div>
        <div class="valor">$ {{ number_format($totalIngresos, 0, ',', '.') }}</div>
    </div>
    <div class="card">
        <div class="label">Efectivo</div>
        <div class="valor">$ {{ number_format($totalEfectivo, 0, ',', '.') }}</div>
    </div>
    <div class="card">
        <div class="label">Transferencia</div>
        <div class="valor">$ {{ number_format($totalTransferencia, 0, ',', '.') }}</div>
    </div>
    <div class="card">
        <div class="label">Credito</div>
        <div class="valor">$ {{ number_format($totalCredito, 0, ',', '.') }}</div>
    </div>
</div>

<table>
    <thead>
        <tr>
            <th>#</th>
            <th>Hora</th>
            <th>Cliente</th>
            <th>Productos</th>
            <th>Pago</th>
            <th style="text-align:right">Total</th>
        </tr>
    </thead>
    <tbody>
        @forelse($ventas as $venta)
        <tr>
            <td>{{ str_pad($venta->id, 6, '0', STR_PAD_LEFT) }}</td>
            <td>{{ $venta->created_at->format('H:i') }}</td>
            <td>{{ $venta->cliente->nombre ?? 'Consumidor final' }}</td>
            <td>
                @foreach($venta->detalles as $d)
                    {{ $d->cantidad }}x {{ $d->nombre_producto }}<br>
                @endforeach
            </td>
            <td>{{ ucfirst($venta->metodo_pago) }}</td>
            <td style="text-align:right;font-weight:bold;">$ {{ number_format($venta->total, 0, ',', '.') }}</td>
        </tr>
        @empty
        <tr><td colspan="6" style="text-align:center;padding:20px;color:#999;">No hay ventas hoy</td></tr>
        @endforelse
    </tbody>
</table>

<div class="footer">Sistema POS Ferretero - Avanzas Digital</div>
</body>
</html>