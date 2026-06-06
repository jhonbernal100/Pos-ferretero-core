@extends('layouts.pos')

@section('titulo', 'Registrar Gasto')

@section('contenido')
<div style="max-width:600px;margin:24px auto;padding:0 16px;">
    <div style="background:#fff;border-radius:12px;padding:24px;">
        <h2 style="font-size:20px;margin-bottom:20px;">💸 Registrar gasto</h2>

        <div id="mensaje" style="display:none;padding:10px;border-radius:8px;margin-bottom:12px;font-size:14px;"></div>

        <div style="margin-bottom:14px;">
            <label style="display:block;font-size:13px;color:#555;margin-bottom:4px;">Descripcion *</label>
            <input type="text" id="f-descripcion" placeholder="Ej: Pago arriendo local"
                style="width:100%;padding:12px;font-size:16px;border:2px solid #ddd;border-radius:8px;">
        </div>

        <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;margin-bottom:14px;">
            <div>
                <label style="display:block;font-size:13px;color:#555;margin-bottom:4px;">Categoria *</label>
                <select id="f-categoria"
                    style="width:100%;padding:12px;font-size:16px;border:2px solid #ddd;border-radius:8px;">
                    <option value="Arriendo">Arriendo</option>
                    <option value="Servicios publicos">Servicios publicos</option>
                    <option value="Nomina">Nomina</option>
                    <option value="Transporte">Transporte</option>
                    <option value="Compras de inventario">Compras de inventario</option>
                    <option value="Mantenimiento">Mantenimiento</option>
                    <option value="Publicidad">Publicidad</option>
                    <option value="Impuestos">Impuestos</option>
                    <option value="Otros">Otros</option>
                </select>
            </div>
            <div>
                <label style="display:block;font-size:13px;color:#555;margin-bottom:4px;">Fecha *</label>
                <input type="date" id="f-fecha" value="{{ now()->format('Y-m-d') }}"
                    style="width:100%;padding:12px;font-size:16px;border:2px solid #ddd;border-radius:8px;">
            </div>
        </div>

        <div style="margin-bottom:14px;">
            <label style="display:block;font-size:13px;color:#555;margin-bottom:4px;">Monto (COP) *</label>
            <input type="number" id="f-monto" placeholder="0" min="1"
                style="width:100%;padding:12px;font-size:20px;border:2px solid #000;border-radius:8px;text-align:right;">
        </div>

        <div style="margin-bottom:20px;">
            <label style="display:block;font-size:13px;color:#555;margin-bottom:4px;">Notas</label>
            <textarea id="f-notas" placeholder="Informacion adicional..."
                style="width:100%;padding:12px;font-size:16px;border:2px solid #ddd;border-radius:8px;height:80px;resize:none;"></textarea>
        </div>

        <button onclick="guardar()"
            style="width:100%;padding:16px;background:#000;color:#fff;border:none;border-radius:10px;font-size:18px;font-weight:bold;cursor:pointer;">
            Guardar gasto
        </button>

        <a href="/gastos"
            style="display:block;text-align:center;margin-top:12px;color:#555;font-size:14px;">
            Volver a gastos
        </a>
    </div>
</div>

@section('scripts')
<script>
async function guardar() {
    const descripcion = document.getElementById('f-descripcion').value;
    const monto       = parseInt(document.getElementById('f-monto').value) || 0;
    const fecha       = document.getElementById('f-fecha').value;

    if (!descripcion) { alert('La descripcion es obligatoria'); return; }
    if (!monto)        { alert('El monto es obligatorio'); return; }
    if (!fecha)        { alert('La fecha es obligatoria'); return; }

    const res = await fetch('/gastos', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
        },
        body: JSON.stringify({
            descripcion: descripcion,
            categoria:   document.getElementById('f-categoria').value,
            monto:       monto,
            fecha:       fecha,
            notas:       document.getElementById('f-notas').value,
        }),
    });

    const data = await res.json();
    const msg  = document.getElementById('mensaje');
    msg.style.display    = 'block';
    msg.style.background = data.success ? '#d4edda' : '#f8d7da';
    msg.style.color      = data.success ? '#155724' : '#721c24';
    msg.textContent      = data.mensaje;

    if (data.success) {
        setTimeout(() => window.location.href = '/gastos', 1500);
    }
}
</script>
@endsection
@endsection