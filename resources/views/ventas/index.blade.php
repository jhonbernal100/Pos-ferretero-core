<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ventas</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; background: #f5f5f5; }
        h1 { font-size: 22px; margin-bottom: 16px; }
        .btn { padding: 10px 20px; background: #000; color: #fff; border: none; border-radius: 6px; cursor: pointer; font-size: 14px; text-decoration: none; display: inline-block; margin-bottom: 16px; }
        table { width: 100%; border-collapse: collapse; background: #fff; border-radius: 8px; overflow: hidden; }
        th { background: #000; color: #fff; padding: 10px; text-align: left; font-size: 13px; }
        td { padding: 10px; border-bottom: 1px solid #eee; font-size: 13px; }
        .badge { padding: 3px 10px; border-radius: 12px; font-size: 11px; font-weight: bold; }
        .badge-completada { background: #d4edda; color: #155724; }
        .badge-anulada { background: #f8d7da; color: #721c24; }
        .link { color: #000; text-decoration: underline; cursor: pointer; }
    </style>
</head>
<body>
    <h1>Ventas del día</h1>
    <a href="{{ route('ventas.crear') }}" class="btn">+ Nueva venta</a>

    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Fecha</th>
                <th>Cliente</th>
                <th>Total</th>
                <th>Pago</th>
                <th>Estado</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            @forelse($ventas as $venta)
            <tr>
                <td>{{ str_pad($venta->id, 6, '0', STR_PAD_LEFT) }}</td>
                <td>{{ $venta->created_at->format('d/m/Y H:i') }}</td>
                <td>{{ $venta->cliente->nombre ?? 'Consumidor final' }}</td>
                <td>$ {{ number_format($venta->total, 0, ',', '.') }}</td>
                <td>{{ ucfirst($venta->metodo_pago) }}</td>
                <td><span class="badge badge-{{ $venta->estado }}">{{ ucfirst($venta->estado) }}</span></td>
                <td>
                    <a href="{{ route('ventas.ticket', $venta->id) }}" class="link" target="_blank">Ticket</a>
                </td>
            </tr>
            @empty
            <tr><td colspan="7" style="text-align:center; color:#999;">No hay ventas aún</td></tr>
            @endforelse
        </tbody>
    </table>

    {{ $ventas->links() }}

    <footer style="text-align:center;padding:16px;font-size:11px;color:#aaa;margin-top:20px;">
    Sistema POS desarrollado por
    <a href="https://www.avanzas.digital/index.html" target="_blank" style="color:#000;font-weight:bold;">
        Avanzas Digital
    </a>
    &nbsp;·&nbsp;
    <a href="https://www.avanzas.digital/index.html" target="_blank" style="color:#000;">
        ¿Quieres este sistema? Contáctanos
    </a>
</footer>
</body>
</html>