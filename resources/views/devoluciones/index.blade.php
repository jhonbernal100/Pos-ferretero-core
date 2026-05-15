@extends('layouts.pos')

@section('titulo', 'Devolución / Cambio')

@section('contenido')
<div style="max-width:900px;margin:24px auto;padding:0 16px;">

    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:16px;">
        <div>
            <h1 style="font-size:22px;">🔄 Devolución / Cambio</h1>
            <p style="color:#555;font-size:14px;">
                Venta #{{ str_pad($venta->id, 6, '0', STR_PAD_LEFT) }} —
                {{ $venta->created_at->format('d/m/Y H:i') }} —
                {{ $venta->cliente->nombre ?? 'Consumidor final' }}
            </p>
        </div>
        <a href="/ventas"
           style="padding:10px 16px;background:#000;color:#fff;border-radius:8px;text-decoration:none;font-size:14px;">
            ← Volver
        </a>
    </div>

    <div id="mensaje" style="display:none;padding:14px;border-radius:8px;margin-bottom:16px;font-size:15px;"></div>

    {{-- Tipo de devolución --}}
    <div style="background:#fff;border-radius:12px;padding:20px;margin-bottom:16px;">
        <h3 style="font-size:16px;margin-bottom:12px;">Tipo de operación</h3>
        <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:12px;">
            <label style="cursor:pointer;">
                <input type="radio" name="tipo" value="devolucion_simple" checked onchange="cambiarTipo(this.value)">
                <div class="tipo-card" id="card-devolucion_simple"
                     style="border:2px solid #000;border-radius:8px;padding:12px;text-align:center;margin-top:4px;background:#000;color:#fff;">
                    <div style="font-size:24px;">💵</div>
                    <div style="font-size:13px;font-weight:bold;margin-top:4px;">Devolución simple</div>
                    <div style="font-size:11px;margin-top:2px;opacity:.8;">Devuelve dinero al cliente</div>
                </div>
            </label>
            <label style="cursor:pointer;">
                <input type="radio" name="tipo" value="cambio_producto" onchange="cambiarTipo(this.value)">
                <div class="tipo-card" id="card-cambio_producto"
                     style="border:2px solid #ddd;border-radius:8px;padding:12px;text-align:center;margin-top:4px;">
                    <div style="font-size:24px;">🔄</div>
                    <div style="font-size:13px;font-weight:bold;margin-top:4px;">Cambio de producto</div>
                    <div style="font-size:11px;margin-top:2px;color:#888;">Devuelve uno y lleva otro</div>
                </div>
            </label>
            <label style="cursor:pointer;">
                <input type="radio" name="tipo" value="devolucion_parcial" onchange="cambiarTipo(this.value)">
                <div class="tipo-card" id="card-devolucion_parcial"
                     style="border:2px solid #ddd;border-radius:8px;padding:12px;text-align:center;margin-top:4px;">
                    <div style="font-size:24px;">📦</div>
                    <div style="font-size:13px;font-weight:bold;margin-top:4px;">Devolución parcial</div>
                    <div style="font-size:11px;margin-top:2px;color:#888;">Devuelve solo algunos items</div>
                </div>
            </label>
        </div>
    </div>

    {{-- Items de la venta original --}}
    <div style="background:#fff;border-radius:12px;padding:20px;margin-bottom:16px;">
        <h3 style="font-size:16px;margin-bottom:12px;">Productos de la venta original</h3>
        <table style="width:100%;border-collapse:collapse;">
            <thead>
                <tr style="background:#f5f5f5;">
                    <th style="padding:10px;text-align:left;font-size:13px;">Devolver</th>
                    <th style="padding:10px;text-align:left;font-size:13px;">Producto</th>
                    <th style="padding:10px;text-align:center;font-size:13px;">Cant. original</th>
                    <th style="padding:10px;text-align:center;font-size:13px;">Cant. a devolver</th>
                    <th style="padding:10px;text-align:right;font-size:13px;">Subtotal</th>
                </tr>
            </thead>
            <tbody>
                @foreach($venta->detalles as $detalle)
                <tr style="border-bottom:1px solid #eee;" id="fila-{{ $detalle->id }}">
                    <td style="padding:10px;text-align:center;">
                        <input type="checkbox"
                               class="chk-devolver"
                               data-id="{{ $detalle->id }}"
                               data-precio="{{ $detalle->precio_unitario }}"
                               data-max="{{ $detalle->cantidad }}"
                               onchange="actualizarResumen()"
                               style="width:20px;height:20px;cursor:pointer;">
                    </td>
                    <td style="padding:10px;font-size:14px;font-weight:bold;">
                        {{ $detalle->nombre_producto }}
                    </td>
                    <td style="padding:10px;text-align:center;font-size:14px;">
                        {{ $detalle->cantidad }}
                    </td>
                    <td style="padding:10px;text-align:center;">
                        <input type="number"
                               class="cant-devolver"
                               data-id="{{ $detalle->id }}"
                               value="{{ $detalle->cantidad }}"
                               min="1"
                               max="{{ $detalle->cantidad }}"
                               onchange="actualizarResumen()"
                               style="width:60px;padding:6px;font-size:16px;text-align:center;border:2px solid #ddd;border-radius:6px;">
                    </td>
                    <td style="padding:10px;text-align:right;font-size:14px;">
                        $ {{ number_format($detalle->subtotal, 0, ',', '.') }}
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    {{-- Productos nuevos — solo visible en cambio --}}
    <div id="panel-productos-nuevos" style="display:none;background:#fff;border-radius:12px;padding:20px;margin-bottom:16px;">
        <h3 style="font-size:16px;margin-bottom:12px;">Productos que lleva el cliente</h3>
        <div id="lista-productos-nuevos"></div>
        <button onclick="agregarProductoNuevo()"
            style="width:100%;padding:12px;background:#f5f5f5;border:2px dashed #ddd;border-radius:8px;font-size:14px;cursor:pointer;margin-top:8px;">
            + Agregar producto
        </button>
    </div>

    {{-- Motivo --}}
    <div style="background:#fff;border-radius:12px;padding:20px;margin-bottom:16px;">
        <h3 style="font-size:16px;margin-bottom:8px;">Motivo de la devolución</h3>
        <textarea id="motivo" placeholder="Ej: Producto defectuoso, talla incorrecta, cambio de opinión..."
            style="width:100%;padding:12px;font-size:14px;border:2px solid #ddd;border-radius:8px;height:80px;resize:none;"></textarea>
    </div>

    {{-- Resumen --}}
    <div style="background:#fff;border-radius:12px;padding:20px;margin-bottom:16px;">
        <h3 style="font-size:16px;margin-bottom:12px;">Resumen</h3>
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
            <div style="background:#f8d7da;border-radius:8px;padding:14px;text-align:center;">
                <div style="font-size:12px;color:#888;margin-bottom:4px;">A devolver al cliente</div>
                <div style="font-size:22px;font-weight:bold;color:#721c24;" id="display-devuelto">$ 0</div>
            </div>
            <div id="panel-cobrar" style="background:#d4edda;border-radius:8px;padding:14px;text-align:center;display:none;">
                <div style="font-size:12px;color:#888;margin-bottom:4px;">A cobrar al cliente</div>
                <div style="font-size:22px;font-weight:bold;color:#155724;" id="display-cobrado">$ 0</div>
            </div>
        </div>
    </div>

    <button onclick="procesarDevolucion()"
        style="width:100%;padding:18px;background:#000;color:#fff;border:none;border-radius:10px;font-size:20px;font-weight:bold;cursor:pointer;">
        ✅ Procesar devolución
    </button>

</div>

<script>
let tipoActual = 'devolucion_simple';
const productos = @json($productos);

function cambiarTipo(tipo) {
    tipoActual = tipo;
    document.querySelectorAll('.tipo-card').forEach(c => {
        c.style.background = '#fff';
        c.style.color = '#000';
        c.style.borderColor = '#ddd';
    });
    const card = document.getElementById('card-' + tipo);
    card.style.background = '#000';
    card.style.color = '#fff';
    card.style.borderColor = '#000';

    document.getElementById('panel-productos-nuevos').style.display =
        tipo === 'cambio_producto' ? 'block' : 'none';

    actualizarResumen();
}

function agregarProductoNuevo() {
    const lista = document.getElementById('lista-productos-nuevos');
    const idx   = lista.children.length;
    const opts  = productos.map(p =>
        `<option value="${p.id}" data-precio="${p.precio_venta}">${p.nombre} — $ ${parseInt(p.precio_venta).toLocaleString('es-CO')}</option>`
    ).join('');

    const div = document.createElement('div');
    div.style.cssText = 'display:grid;grid-template-columns:1fr auto auto;gap:8px;margin-bottom:8px;align-items:center;';
    div.innerHTML = `
        <select class="select-prod-nuevo" onchange="actualizarResumen()"
            style="padding:10px;font-size:14px;border:2px solid #ddd;border-radius:8px;">
            <option value="">Seleccionar producto</option>
            ${opts}
        </select>
        <input type="number" class="cant-prod-nuevo" value="1" min="1"
               onchange="actualizarResumen()"
               style="width:70px;padding:10px;font-size:16px;text-align:center;border:2px solid #ddd;border-radius:8px;">
        <button onclick="this.parentElement.remove();actualizarResumen();"
            style="padding:10px;background:#c00;color:#fff;border:none;border-radius:8px;cursor:pointer;font-size:16px;">✕</button>
    `;
    lista.appendChild(div);
}

function actualizarResumen() {
    let montoDevuelto = 0;
    let montoCobrado  = 0;

    document.querySelectorAll('.chk-devolver').forEach(chk => {
        if (chk.checked) {
            const id     = chk.dataset.id;
            const precio = parseInt(chk.dataset.precio);
            const cant   = parseInt(document.querySelector(`.cant-devolver[data-id="${id}"]`).value) || 0;
            montoDevuelto += precio * cant;
        }
    });

    if (tipoActual === 'cambio_producto') {
        let montoNuevo = 0;
        document.querySelectorAll('.select-prod-nuevo').forEach((sel, i) => {
            if (!sel.value) return;
            const opt    = sel.options[sel.selectedIndex];
            const precio = parseInt(opt.dataset.precio) || 0;
            const cant   = parseInt(document.querySelectorAll('.cant-prod-nuevo')[i].value) || 0;
            montoNuevo  += precio * cant;
        });
        montoCobrado = Math.max(0, montoNuevo - montoDevuelto);
        montoDevuelto = Math.max(0, montoDevuelto - montoNuevo);
    }

    document.getElementById('display-devuelto').textContent =
        '$ ' + montoDevuelto.toLocaleString('es-CO');

    const panelCobrar = document.getElementById('panel-cobrar');
    if (montoCobrado > 0) {
        panelCobrar.style.display = 'block';
        document.getElementById('display-cobrado').textContent =
            '$ ' + montoCobrado.toLocaleString('es-CO');
    } else {
        panelCobrar.style.display = 'none';
    }
}

async function procesarDevolucion() {
    const itemsDevueltos = [];

    document.querySelectorAll('.chk-devolver').forEach(chk => {
        const id   = chk.dataset.id;
        const cant = parseInt(document.querySelector(`.cant-devolver[data-id="${id}"]`).value) || 0;
        itemsDevueltos.push({
            detalle_id: id,
            devolver:   chk.checked,
            cantidad:   cant,
        });
    });

    const hayDevolucion = itemsDevueltos.some(i => i.devolver);
    if (!hayDevolucion) {
        alert('Selecciona al menos un producto a devolver');
        return;
    }

    const itemsNuevos = [];
    if (tipoActual === 'cambio_producto') {
        document.querySelectorAll('.select-prod-nuevo').forEach((sel, i) => {
            if (!sel.value) return;
            const cant = parseInt(document.querySelectorAll('.cant-prod-nuevo')[i].value) || 0;
            itemsNuevos.push({ producto_id: sel.value, cantidad: cant });
        });
    }

    const payload = {
        tipo:            tipoActual,
        items_devueltos: itemsDevueltos,
        items_nuevos:    itemsNuevos,
        motivo:          document.getElementById('motivo').value,
    };

    const res = await fetch('/ventas/{{ $venta->id }}/devolucion', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
        },
        body: JSON.stringify(payload),
    });

    const data = await res.json();
    const msg  = document.getElementById('mensaje');
    msg.style.display    = 'block';
    msg.style.background = data.success ? '#d4edda' : '#f8d7da';
    msg.style.color      = data.success ? '#155724' : '#721c24';
    msg.style.padding    = '14px';
    msg.style.borderRadius = '8px';
    msg.textContent      = data.mensaje;

    if (data.success) {
        if (data.venta_nueva_id) {
            window.open(`/ventas/${data.venta_nueva_id}/ticket`, '_blank');
        }
        setTimeout(() => window.location.href = '/ventas', 2000);
    }
}
</script>
@endsection