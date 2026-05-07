@extends('layouts.pos')

@section('titulo', 'Historial de crédito')

@section('contenido')
<div style="padding:16px;max-width:900px;margin:0 auto;">

    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:16px;">
        <div>
            <h1 style="font-size:22px;">💳 Historial de crédito</h1>
            <p style="color:#555;font-size:14px;">{{ $cliente->nombre }}</p>
        </div>
        <a href="/clientes/{{ $cliente->id }}/creditos"
           style="padding:10px 16px;background:#000;color:#fff;border-radius:8px;text-decoration:none;font-size:14px;">
            ← Volver
        </a>
    </div>

    @if($credito)
    <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:12px;margin-bottom:20px;">
        <div style="background:#fff;border-radius:10px;padding:16px;text-align:center;">
            <div style="font-size:12px;color:#888;margin-bottom:4px;">Tope de crédito</div>
            <div style="font-size:20px;font-weight:bold;">
                $ {{ number_format($credito->tope_credito, 0, ',', '.') }}
            </div>
        </div>
        <div style="background:#f8d7da;border-radius:10px;padding:16px;text-align:center;">
            <div style="font-size:12px;color:#888;margin-bottom:4px;">Saldo usado</div>
            <div style="font-size:20px;font-weight:bold;color:#721c24;">
                $ {{ number_format($credito->saldo_usado, 0, ',', '.') }}
            </div>
        </div>
        <div style="background:#d4edda;border-radius:10px;padding:16px;text-align:center;">
            <div style="font-size:12px;color:#888;margin-bottom:4px;">Disponible</div>
            <div style="font-size:20px;font-weight:bold;color:#155724;">
                $ {{ number_format($credito->saldoDisponible(), 0, ',', '.') }}
            </div>
        </div>
    </div>

    @if($credito->saldo_usado > 0)
    <div style="background:#fff;border-radius:12px;padding:20px;margin-bottom:20px;border:2px solid #000;">
        <h3 style="font-size:16px;margin-bottom:12px;">💵 Registrar pago del cliente</h3>

        <div id="msg-pago" style="display:none;padding:10px;border-radius:8px;margin-bottom:12px;font-size:14px;"></div>

        <div style="display:grid;grid-template-columns:1fr auto;gap:12px;align-items:end;">
            <div>
                <label style="display:block;font-size:13px;color:#555;margin-bottom:4px;">
                    Monto a pagar (máx: $ {{ number_format($credito->saldo_usado, 0, ',', '.') }})
                </label>
                <input type="number" id="monto-pago"
                    value="{{ $credito->saldo_usado }}"
                    max="{{ $credito->saldo_usado }}"
                    min="1"
                    style="width:100%;padding:12px;font-size:20px;border:2px solid #000;border-radius:8px;text-align:right;">
            </div>
            <button onclick="registrarPago()"
                style="padding:14px 20px;background:#28a745;color:#fff;border:none;border-radius:8px;font-size:16px;font-weight:bold;cursor:pointer;white-space:nowrap;">
                ✅ Registrar pago
            </button>
        </div>
        <p style="font-size:12px;color:#888;margin-top:8px;">
            Al registrar el pago total el crédito quedará en $0 y se habilitará nuevamente.
        </p>
    </div>
    @endif
    @endif

    <div style="background:#fff;border-radius:12px;overflow:hidden;">
        <div style="padding:16px;border-bottom:1px solid #eee;display:flex;justify-content:space-between;align-items:center;">
            <span style="font-weight:bold;font-size:16px;">Tickets de crédito</span>
            <span style="font-size:13px;color:#555;">{{ $ventas->count() }} ticket(s)</span>
        </div>
        <table style="width:100%;border-collapse:collapse;">
            <thead>
                <tr style="background:#f5f5f5;">
                    <th style="padding:12px;text-align:left;font-size:13px;"># Ticket</th>
                    <th style="padding:12px;text-align:left;font-size:13px;">Fecha</th>
                    <th style="padding:12px;text-align:left;font-size:13px;">Productos</th>
                    <th style="padding:12px;text-align:right;font-size:13px;">Total</th>
                    <th style="padding:12px;text-align:center;font-size:13px;">Estado</th>
                    <th style="padding:12px;text-align:center;font-size:13px;">Ticket</th>
                </tr>
            </thead>
            <tbody>
                @forelse($ventas as $venta)
                <tr style="border-bottom:1px solid #eee;background:{{ $venta->credito_pagado ? '#f9fff9' : '#fff' }}">
                    <td style="padding:12px;font-size:13px;font-weight:bold;">
                        {{ str_pad($venta->id, 6, '0', STR_PAD_LEFT) }}
                    </td>
                    <td style="padding:12px;font-size:13px;">
                        {{ $venta->created_at->format('d/m/Y H:i') }}
                    </td>
                    <td style="padding:12px;font-size:12px;color:#555;">
                        @foreach($venta->detalles as $detalle)
                            <div>{{ $detalle->cantidad }}x {{ $detalle->nombre_producto }}</div>
                        @endforeach
                    </td>
                    <td style="padding:12px;font-size:14px;font-weight:bold;text-align:right;">
                        $ {{ number_format($venta->total, 0, ',', '.') }}
                    </td>
                    <td style="padding:12px;text-align:center;">
                        @if($venta->credito_pagado)
                            <span style="padding:4px 10px;background:#d4edda;color:#155724;border-radius:12px;font-size:11px;font-weight:bold;">
                                ✅ Pagado
                            </span>
                        @else
                            <span style="padding:4px 10px;background:#fff3cd;color:#856404;border-radius:12px;font-size:11px;font-weight:bold;">
                                ⏳ Pendiente
                            </span>
                        @endif
                    </td>
                    <td style="padding:12px;text-align:center;">
                        <a href="/ventas/{{ $venta->id }}/ticket" target="_blank"
                           style="padding:6px 12px;background:#000;color:#fff;border-radius:6px;font-size:12px;text-decoration:none;">
                            🖨 Imprimir
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" style="padding:24px;text-align:center;color:#999;">
                        No hay ventas a crédito para este cliente
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>

        @if($ventas->count() > 0)
        <div style="padding:16px;text-align:right;border-top:2px solid #000;">
            <div style="font-size:13px;color:#555;margin-bottom:4px;">
                Pendiente: $ {{ number_format($ventas->where('credito_pagado', false)->sum('total'), 0, ',', '.') }}
            </div>
            <div style="font-size:16px;font-weight:bold;">
                Total acumulado: $ {{ number_format($ventas->sum('total'), 0, ',', '.') }}
            </div>
        </div>
        @endif
    </div>
</div>

<script>
async function registrarPago() {
    const monto = parseInt(document.getElementById('monto-pago').value) || 0;
    if (!monto || monto <= 0) {
        alert('Ingresa un monto válido');
        return;
    }

    if (!confirm(`¿Confirmas el pago de $ ${monto.toLocaleString('es-CO')}?`)) return;

    const res = await fetch('/clientes/{{ $cliente->id }}/pagar-credito', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
        },
        body: JSON.stringify({ monto_pagado: monto }),
    });

    const data = await res.json();
    const msg  = document.getElementById('msg-pago');
    msg.style.display    = 'block';
    msg.style.background = data.success ? '#d4edda' : '#f8d7da';
    msg.style.color      = data.success ? '#155724' : '#721c24';
    msg.textContent      = data.mensaje;

    if (data.success) {
        setTimeout(() => window.location.reload(), 1500);
    }
}
</script>
@endsection