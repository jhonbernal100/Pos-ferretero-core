<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<style>
    * { margin:0; padding:0; box-sizing:border-box; }
    body { font-family: Arial, sans-serif; font-size: 11px; }
    .header { background: #856404; color: #fff; padding: 12px 16px; margin-bottom: 16px; }
    .header h1 { font-size: 18px; }
    .header p { font-size: 11px; opacity:.8; margin-top:2px; }
    table { width: calc(100% - 32px); margin: 0 16px; border-collapse: collapse; }
    th { background: #856404; color: #fff; padding: 8px; font-size: 10px; text-align: left; }
    td { padding: 7px 8px; font-size: 11px; border-bottom: 1px solid #eee; }
    .agotado { background: #f8d7da; }
    .bajo { background: #fff3cd; }
    .footer { margin-top:16px; text-align:center; font-size:9px; color:#aaa; padding:8px; border-top:1px solid #eee; }
</style>
</head>
<body>

<div class="header">
    <h1>⚠️ Alerta de Stock Bajo</h1>
    <p>Generado el {{ now()->format('d/m/Y H:i') }} — {{ auth()->user()->tenant->nombre ?? '' }}</p>
</div>

<table>
    <thead>
        <tr>
            <th>Producto</th>
            <th>Categoría</th>
            <th style="text-align:center">Stock actual</th>
            <th style="text-align:center">Stock mínimo</th>
            <th>Estado</th>
        </tr>
    </thead>
    <tbody>
        @foreach($productos as $p)
        <tr class="{{ $p->stock <= 0 ? 'agotado' : 'bajo' }}">
            <td><strong>{{ $p->nombre }}</strong><br><small>{{ $p->marca }}</small></td>
            <td>{{ $p->categoria }}</td>
            <td style="text-align:center;font-weight:bold;">{{ $p->stock }}</td>
            <td style="text-align:center;">{{ $p->stock_minimo }}</td>
            <td>{{ $p->stock <= 0 ? '⛔ Agotado' : '⚠️ Stock bajo' }}</td>
        </tr>
        @endforeach
    </tbody>
</table>

<div class="footer">
    Sistema POS Ferretero — Avanzas Digital
</div>
</body>
</html>