<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<style>
    * { margin:0; padding:0; box-sizing:border-box; }
    body { font-family: Arial, sans-serif; font-size: 11px; }
    .header { background: #000; color: #fff; padding: 12px 16px; margin-bottom: 16px; }
    .info-producto { margin: 0 16px 16px; background:#f5f5f5; border-radius:6px; padding:12px; }
    table { width:calc(100% - 32px); margin:0 16px; border-collapse:collapse; }
    th { background:#000; color:#fff; padding:8px; font-size:10px; text-align:left; }
    td { padding:6px 8px; font-size:10px; border-bottom:1px solid #eee; text-align:center; }
    td:first-child { text-align:left; }
    .salida { color:#721c24; font-weight:bold; }
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
            </td>
            <td style="width:30%;text-align:right;vertical-align:middle;">
                <p style="font-size:13px;font-weight:bold;color:#fff;">KARDEX DE PRODUCTO</p>
                <p style="font-size:10px;opacity:.8;">{{ now()->format('d/m/Y') }}</p>
            </td>
        </tr>
    </table>
</div>

<div class="info-producto">
    <strong>{{ $producto->nombre }}</strong>
    @if($producto->marca) - {{ $producto->marca }} @endif
    <br>
    Categoria: {{ $producto->categoria }} | Unidad: {{ $producto->unidad }}
    <br>
    Precio costo: $ {{ number_format($producto->precio_compra, 0, ',', '.') }} |
    Precio venta: $ {{ number_format($producto->precio_venta, 0, ',', '.') }} |
    <strong>Stock actual: {{ $producto->stock }}</strong>
</div>

<table>
    <thead>
        <tr>
            <th>Fecha</th>
            <th>Tipo</th>
            <th>Documento</th>
            <th>Entrada</th>
            <th>Salida</th>
            <th>P. Unitario</th>
            <th>Total</th>
        </tr>
    </thead>
    <tbody>
        @forelse($salidas as $s)
        <tr>
            <td style="text-align:left;">{{ $s->created_at->format('d/m/Y H:i') }}</td>
            <td>Venta</td>
            <td>{{ str_pad($s->venta_id, 6, '0', STR_PAD_LEFT) }}</td>
            <td>-</td>
            <td class="salida">{{ $s->cantidad }}</td>
            <td>$ {{ number_format($s->precio_unitario, 0, ',', '.') }}</td>
            <td>$ {{ number_format($s->subtotal, 0, ',', '.') }}</td>
        </tr>
        @empty
        <tr>
            <td colspan="7" style="text-align:center;padding:20px;color:#999;">
                Sin movimientos registrados
            </td>
        </tr>
        @endforelse
    </tbody>
</table>

<div class="footer">Sistema POS Ferretero - Avanzas Digital</div>
</body>
</html>