@extends('layouts.pos')

@section('titulo', 'Historial de Ventas')

@section('contenido')
<div style="padding:16px;max-width:1200px;margin:0 auto;">
    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:16px;">
        <h1 style="font-size:22px;">Ventas del día</h1>
        <a href="/ventas/crear" style="padding:10px 20px;background:#000;color:#fff;border-radius:8px;text-decoration:none;font-size:14px;">+ Nueva venta</a>
    </div>

    <div style="background:#fff;border-radius:12px;overflow:hidden;">
        <table style="width:100%;border-collapse:collapse;">
            <thead>
                <tr style="background:#000;color:#fff;">
                    <th style="padding:12px;text-align:left;font-size:13px;">#</th>
                    <th style="padding:12px;text-align:left;font-size:13px;">Fecha</th>
                    <th style="padding:12px;text-align:left;font-size:13px;">Cliente</th>
                    <th style="padding:12px;text-align:left;font-size:13px;">Total</th>
                    <th style="padding:12px;text-align:left;font-size:13px;">Pago</th>
                    <th style="padding:12px;text-align:left;font-size:13px;">Estado</th>
                    <th style="padding:12px;text-align:left;font-size:13px;">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse($ventas as $venta)
                <tr style="border-bottom:1px solid #eee;">
                    <td style="padding:12px;font-size:13px;">{{ str_pad($venta->id, 6, '0', STR_PAD_LEFT) }}</td>
                    <td style="padding:12px;font-size:13px;">{{ $venta->created_at->format('d/m/Y H:i') }}</td>
                    <td style="padding:12px;font-size:13px;">{{ $venta->cliente->nombre ?? 'Consumidor final' }}</td>
                    <td style="padding:12px;font-size:13px;">$ {{ number_format($venta->total, 0, ',', '.') }}</td>
                    <td style="padding:12px;font-size:13px;">{{ ucfirst($venta->metodo_pago) }}</td>
                    <td style="padding:12px;">
                        <span style="padding:3px 10px;border-radius:12px;font-size:11px;font-weight:bold;
                            background:{{ $venta->estado === 'completada' ? '#d4edda' : '#f8d7da' }};
                            color:{{ $venta->estado === 'completada' ? '#155724' : '#721c24' }};">
                            {{ ucfirst($venta->estado) }}
                        </span>
                    </td>
                    <td style="padding:12px;">
                        <a href="/ventas/{{ $venta->id }}/ticket" target="_blank"
                           style="color:#000;text-decoration:underline;font-size:13px;">
                            Ticket
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" style="padding:24px;text-align:center;color:#999;">No hay ventas aún</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div style="margin-top:16px;">
        {{ $ventas->links() }}
    </div>
</div>
@endsection