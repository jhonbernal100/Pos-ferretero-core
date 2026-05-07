@extends('layouts.pos')

@section('titulo', 'Crédito del cliente')

@section('contenido')
<div style="max-width:600px;margin:24px auto;padding:0 16px;">
    <div style="background:#fff;border-radius:12px;padding:24px;">
        <h2 style="font-size:20px;margin-bottom:4px;">💳 Crédito</h2>
        <p style="color:#555;font-size:14px;margin-bottom:20px;">{{ $cliente->nombre }}</p>

        {{-- Alerta crédito pagado --}}
        @if($credito && $credito->estado === 'pagado')
        <div style="background:#d4edda;border:2px solid #28a745;border-radius:10px;padding:16px;margin-bottom:20px;text-align:center;">
            <div style="font-size:24px;margin-bottom:8px;">✅</div>
            <div style="font-size:16px;font-weight:bold;color:#155724;">Crédito pagado</div>
            <div style="font-size:13px;color:#555;margin-top:4px;">
                Para habilitar nuevas compras a crédito, define un nuevo tope y cambia el estado a <strong>Activo</strong>.
            </div>
        </div>
        @endif

        {{-- Alerta crédito bloqueado --}}
        @if($credito && $credito->estado === 'bloqueado')
        <div style="background:#fff3cd;border:2px solid #ffc107;border-radius:10px;padding:16px;margin-bottom:20px;text-align:center;">
            <div style="font-size:24px;margin-bottom:8px;">⛔</div>
            <div style="font-size:16px;font-weight:bold;color:#856404;">Crédito bloqueado</div>
            <div style="font-size:13px;color:#555;margin-top:4px;">
                Este cliente no puede realizar compras a crédito. Cambia el estado a <strong>Activo</strong> para habilitarlo.
            </div>
        </div>
        @endif

        {{-- Resumen de saldos --}}
        @if($credito)
        <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:12px;margin-bottom:20px;">
            <div style="background:#f5f5f5;border-radius:8px;padding:14px;text-align:center;">
                <div style="font-size:12px;color:#888;margin-bottom:4px;">Tope</div>
                <div style="font-size:18px;font-weight:bold;">
                    $ {{ number_format($credito->tope_credito, 0, ',', '.') }}
                </div>
            </div>
            <div style="background:#f8d7da;border-radius:8px;padding:14px;text-align:center;">
                <div style="font-size:12px;color:#888;margin-bottom:4px;">Usado</div>
                <div style="font-size:18px;font-weight:bold;color:#721c24;">
                    $ {{ number_format($credito->saldo_usado, 0, ',', '.') }}
                </div>
            </div>
            <div style="background:#d4edda;border-radius:8px;padding:14px;text-align:center;">
                <div style="font-size:12px;color:#888;margin-bottom:4px;">Disponible</div>
                <div style="font-size:18px;font-weight:bold;color:#155724;">
                    $ {{ number_format($credito->saldoDisponible(), 0, ',', '.') }}
                </div>
            </div>
        </div>
        @endif

        <div id="mensaje" style="display:none;padding:10px;border-radius:8px;margin-bottom:12px;font-size:14px;"></div>

        <div style="margin-bottom:14px;">
            <label style="display:block;font-size:13px;color:#555;margin-bottom:4px;">Tope de crédito (COP)</label>
            <input type="number" id="f-tope" value="{{ $credito->tope_credito ?? 0 }}"
                style="width:100%;padding:12px;font-size:20px;border:2px solid #000;border-radius:8px;text-align:right;">
            <span style="font-size:12px;color:#888;">Define el monto máximo que puede deber este cliente</span>
        </div>

        <div style="margin-bottom:14px;">
            <label style="display:block;font-size:13px;color:#555;margin-bottom:4px;">Estado</label>
            <select id="f-estado"
                style="width:100%;padding:12px;font-size:16px;border:2px solid #ddd;border-radius:8px;">
                <option value="activo"    {{ ($credito->estado ?? '') === 'activo'    ? 'selected' : '' }}>✅ Activo — puede comprar a crédito</option>
                <option value="bloqueado" {{ ($credito->estado ?? '') === 'bloqueado' ? 'selected' : '' }}>⛔ Bloqueado — no puede comprar</option>
                <option value="pagado"    {{ ($credito->estado ?? '') === 'pagado'    ? 'selected' : '' }}>💵 Pagado — saldo en cero</option>
            </select>
        </div>

        <div style="margin-bottom:20px;">
            <label style="display:block;font-size:13px;color:#555;margin-bottom:4px;">Notas</label>
            <textarea id="f-notas"
                style="width:100%;padding:12px;font-size:16px;border:2px solid #ddd;border-radius:8px;height:80px;resize:none;">{{ $credito->notas ?? '' }}</textarea>
        </div>

        <button onclick="guardar()"
            style="width:100%;padding:16px;background:#000;color:#fff;border:none;border-radius:10px;font-size:18px;font-weight:bold;cursor:pointer;">
            💾 Guardar cambios
        </button>

        <a href="/clientes/{{ $cliente->id }}/historial"
           style="display:block;text-align:center;margin-top:12px;padding:12px;background:#f5f5f5;border-radius:8px;color:#000;text-decoration:none;font-size:14px;">
            📋 Ver historial de consumo
        </a>

        <a href="/clientes"
           style="display:block;text-align:center;margin-top:8px;color:#555;font-size:14px;">
            ← Volver a clientes
        </a>
    </div>
</div>

<script>
async function guardar() {
    const res = await fetch('/clientes/{{ $cliente->id }}/creditos', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
        },
        body: JSON.stringify({
            tope_credito: parseInt(document.getElementById('f-tope').value) || 0,
            estado:       document.getElementById('f-estado').value,
            notas:        document.getElementById('f-notas').value,
        }),
    });

    const data = await res.json();
    const msg  = document.getElementById('mensaje');
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