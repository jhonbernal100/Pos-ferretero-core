@extends('layouts.pos')

@section('titulo', 'Nueva Venta')

@section('estilos')
<style>
    .layout { display: grid; grid-template-columns: 1fr 380px; height: calc(100vh - 90px); }
    .panel-productos { padding: 16px; overflow-y: auto; }
    .buscador { width: 100%; padding: 14px; font-size: 18px; border: 2px solid #000; border-radius: 8px; margin-bottom: 16px; }
    .grid-productos { display: grid; grid-template-columns: repeat(auto-fill, minmax(160px, 1fr)); gap: 12px; }
    .producto-card { background: #fff; border: 2px solid #ddd; border-radius: 10px; padding: 14px; cursor: pointer; transition: border-color .2s, transform .1s; }
    .producto-card:hover { border-color: #000; transform: scale(1.02); }
    .producto-card.sin-stock { opacity: .5; cursor: not-allowed; }
    .producto-card .nombre { font-size: 16px; font-weight: bold; margin-bottom: 6px; line-height: 1.3; }
    .producto-card .precio { font-size: 22px; font-weight: bold; color: #000; }
    .producto-card .stock { font-size: 12px; margin-top: 4px; }
    .stock-ok { color: #155724; }
    .stock-bajo { color: #856404; }
    .stock-cero { color: #721c24; }
    .panel-carrito { background: #fff; display: flex; flex-direction: column; border-left: 2px solid #ddd; }
    .carrito-header { padding: 16px; border-bottom: 2px solid #eee; font-size: 18px; font-weight: bold; }
    .carrito-items { flex: 1; overflow-y: auto; padding: 12px; }
    .carrito-item { display: flex; align-items: center; gap: 8px; padding: 10px 0; border-bottom: 1px solid #eee; }
    .carrito-item .item-nombre { flex: 1; font-size: 13px; font-weight: bold; }
    .carrito-item .item-precio { font-size: 13px; color: #555; min-width: 70px; text-align: right; }
    .btn-cant { width: 36px; height: 36px; border: 1px solid #ddd; background: #f5f5f5; border-radius: 6px; font-size: 20px; cursor: pointer; display: flex; align-items: center; justify-content: center; }
    .cant-num { width: 28px; text-align: center; font-size: 15px; font-weight: bold; }
    .btn-quitar { background: none; border: none; color: #c00; font-size: 20px; cursor: pointer; padding: 0 4px; }
    .carrito-footer { padding: 16px; border-top: 2px solid #eee; }
    .fila-total { display: flex; justify-content: space-between; font-size: 15px; margin-bottom: 8px; }
    .fila-total.grande { font-size: 24px; font-weight: bold; margin: 12px 0; }
    .select-pago { width: 100%; padding: 12px; font-size: 16px; border: 2px solid #ddd; border-radius: 8px; margin-bottom: 10px; }
    .input-pago { width: 100%; padding: 12px; font-size: 22px; border: 2px solid #000; border-radius: 8px; margin-bottom: 10px; text-align: right; }
    .cambio-display { background: #000; color: #fff; text-align: center; padding: 10px; border-radius: 8px; font-size: 22px; font-weight: bold; margin-bottom: 10px; display: none; }
    .btn-cobrar { width: 100%; padding: 22px; background: #000; color: #fff; border: none; border-radius: 10px; font-size: 26px; font-weight: bold; cursor: pointer; letter-spacing: 1px; }
    .btn-cobrar:disabled { background: #aaa; cursor: not-allowed; }
    .btn-limpiar { width: 100%; padding: 10px; background: none; border: 1px solid #ddd; border-radius: 8px; font-size: 14px; cursor: pointer; margin-top: 8px; color: #c00; }
    .select-cliente { width: 100%; padding: 10px; font-size: 14px; border: 1px solid #ddd; border-radius: 8px; margin-bottom: 10px; }
    .empty-carrito { text-align: center; color: #aaa; margin-top: 40px; font-size: 15px; }

    /* Imagen con skeleton */
    .img-wrapper {
        width: 100%;
        height: 80px;
        margin-bottom: 8px;
        border-radius: 6px;
        overflow: hidden;
        background: #f0f0f0;
        position: relative;
    }

    .img-wrapper img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        opacity: 0;
        transition: opacity 0.3s ease;
        position: absolute;
        top: 0; left: 0;
    }

    .img-wrapper img.loaded { opacity: 1; }

    .img-placeholder {
        position: absolute;
        top: 0; left: 0;
        width: 100%; height: 100%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 28px;
        background: #f5f5f5;
        transition: opacity 0.3s ease;
        z-index: 1;
    }

    .img-placeholder.oculto { opacity: 0; pointer-events: none; }

    @media (max-width: 768px) {
        .layout { grid-template-columns: 1fr; grid-template-rows: 1fr auto; height: auto; }
        .panel-carrito { border-left: none; border-top: 2px solid #ddd; max-height: 45vh; }
        .grid-productos { grid-template-columns: repeat(auto-fill, minmax(140px, 1fr)); }
        .btn-cobrar { font-size: 26px; padding: 20px; }
        .buscador { font-size: 20px; padding: 16px; }
    }
</style>
@endsection

@section('contenido')
<div class="layout">
    <div class="panel-productos" id="panel-scroll">
        <input type="text" class="buscador" id="buscador"
               placeholder="Buscar producto..." autofocus>
        <div class="grid-productos" id="grid-productos">
        @foreach($productos as $producto)
        <div class="producto-card {{ $producto->stock <= 0 ? 'sin-stock' : '' }}"
            data-id="{{ $producto->id }}"
            data-nombre="{{ $producto->nombre }}"
            data-precio="{{ $producto->precio_venta }}"
            data-stock="{{ $producto->stock }}"
            onclick="agregarAlCarrito(this)">

            <div class="img-wrapper">
                @if($producto->foto)
                    <div class="img-placeholder" id="ph-{{ $producto->id }}">🔩</div>
                    <img data-src="{{ asset('storage/' . $producto->foto) }}"
                         src="data:image/gif;base64,R0lGODlhAQABAAAAACH5BAEKAAEALAAAAAABAAEAAAICTAEAOw=="
                         alt="{{ $producto->nombre }}"
                         class="lazy-img"
                         data-id="{{ $producto->id }}"
                         onerror="this.style.display='none'">
                @else
                    <div class="img-placeholder">🔩</div>
                @endif
            </div>

            <div class="nombre">{{ $producto->nombre }}</div>
            <div class="precio">$ {{ number_format($producto->precio_venta, 0, ',', '.') }}</div>
            <div class="stock {{ $producto->stock > $producto->stock_minimo ? 'stock-ok' : ($producto->stock > 0 ? 'stock-bajo' : 'stock-cero') }}">
                {{ $producto->stock > 0 ? 'Stock: ' . $producto->stock : 'Sin stock' }}
            </div>
        </div>
        @endforeach
        </div>
    </div>

    <div class="panel-carrito">
        <div class="carrito-header">Venta actual</div>
        <div class="carrito-items" id="carrito-items">
            <div class="empty-carrito">Toca un producto para agregarlo</div>
        </div>
        <div class="carrito-footer">
            <select class="select-cliente" id="select-cliente">
                <option value="">Consumidor final</option>
                @foreach($clientes as $cliente)
                <option value="{{ $cliente->id }}">{{ $cliente->nombre }}</option>
                @endforeach
            </select>
            <div class="fila-total">
                <span>Subtotal</span>
                <span id="display-subtotal">$ 0</span>
            </div>
            <div class="fila-total grande">
                <span>TOTAL</span>
                <span id="display-total">$ 0</span>
            </div>
            <select class="select-pago" id="select-pago" onchange="actualizarCambio()">
                <option value="efectivo">Efectivo</option>
                <option value="transferencia">Transferencia</option>
                <option value="credito">Credito</option>
            </select>
            <input type="number" class="input-pago" id="input-pago"
                   placeholder="Monto recibido" oninput="actualizarCambio()">
            <div class="cambio-display" id="cambio-display">
                CAMBIO: $ <span id="display-cambio">0</span>
            </div>
            <button class="btn-cobrar" id="btn-cobrar"
                    onclick="procesarVenta()" disabled>
                COBRAR
            </button>
            <button class="btn-limpiar" onclick="limpiarCarrito()">
                Cancelar venta
            </button>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
let carrito = [];

function formatCOP(n) {
    return '$ ' + parseInt(n).toLocaleString('es-CO');
}

function agregarAlCarrito(card) {
    if (card.classList.contains('sin-stock')) return;
    const id        = card.dataset.id;
    const nombre    = card.dataset.nombre;
    const precio    = parseInt(card.dataset.precio);
    const existente = carrito.find(i => i.id === id);
    if (existente) {
        existente.cantidad++;
    } else {
        carrito.push({ id, nombre, precio, cantidad: 1 });
    }
    renderCarrito();
}

function renderCarrito() {
    const container = document.getElementById('carrito-items');
    if (carrito.length === 0) {
        container.innerHTML = '<div class="empty-carrito">Toca un producto para agregarlo</div>';
        actualizarTotales();
        return;
    }
    container.innerHTML = carrito.map((item, idx) => `
        <div class="carrito-item">
            <div class="item-nombre">${item.nombre}</div>
            <button class="btn-cant" onclick="cambiarCant(${idx}, -1)">−</button>
            <div class="cant-num">${item.cantidad}</div>
            <button class="btn-cant" onclick="cambiarCant(${idx}, 1)">+</button>
            <div class="item-precio">${formatCOP(item.precio * item.cantidad)}</div>
            <button class="btn-quitar" onclick="quitarItem(${idx})">✕</button>
        </div>
    `).join('');
    actualizarTotales();
}

function cambiarCant(idx, delta) {
    carrito[idx].cantidad += delta;
    if (carrito[idx].cantidad <= 0) carrito.splice(idx, 1);
    renderCarrito();
}

function quitarItem(idx) {
    carrito.splice(idx, 1);
    renderCarrito();
}

function actualizarTotales() {
    const subtotal = carrito.reduce((s, i) => s + i.precio * i.cantidad, 0);
    document.getElementById('display-subtotal').textContent = formatCOP(subtotal);
    document.getElementById('display-total').textContent    = formatCOP(subtotal);
    document.getElementById('btn-cobrar').disabled          = carrito.length === 0;
    actualizarCambio();
}

function actualizarCambio() {
    const total  = carrito.reduce((s, i) => s + i.precio * i.cantidad, 0);
    const pagado = parseInt(document.getElementById('input-pago').value) || 0;
    const cambio = pagado - total;
    const box    = document.getElementById('cambio-display');
    if (pagado > 0 && cambio >= 0) {
        document.getElementById('display-cambio').textContent =
            parseInt(cambio).toLocaleString('es-CO');
        box.style.display = 'block';
    } else {
        box.style.display = 'none';
    }
}

function limpiarCarrito() {
    carrito = [];
    document.getElementById('input-pago').value = '';
    renderCarrito();
}

async function procesarVenta() {
    const total   = carrito.reduce((s, i) => s + i.precio * i.cantidad, 0);
    const pagado  = parseInt(document.getElementById('input-pago').value) || 0;
    const metodo  = document.getElementById('select-pago').value;
    const cliente = document.getElementById('select-cliente').value;

    if (metodo === 'efectivo' && pagado < total) {
        alert('El monto recibido es menor al total');
        return;
    }

    const btn = document.getElementById('btn-cobrar');
    btn.disabled    = true;
    btn.textContent = 'Procesando...';

    const payload = {
        items: carrito.map(i => ({
            producto_id: i.id,
            cantidad:    i.cantidad,
        })),
        metodo_pago:  metodo,
        monto_pagado: pagado || total,
        cliente_id:   cliente || null,
    };

    try {
        const token = document.querySelector('meta[name="csrf-token"]').content;
        const res = await fetch('/ventas', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': token,
            },
            credentials: 'same-origin',
            body: JSON.stringify(payload),
        });

        if (res.status === 419) {
            alert('La sesion expiro. La pagina se recargara.');
            window.location.reload();
            return;
        }

        const data = await res.json();

        if (data.success) {
            window.open(`/ventas/${data.venta_id}/ticket`, '_blank');
            limpiarCarrito();
        } else {
            alert('Error: ' + data.mensaje);
            btn.disabled    = false;
            btn.textContent = 'COBRAR';
        }
    } catch (e) {
        alert('Error de conexion: ' + e.message);
        btn.disabled    = false;
        btn.textContent = 'COBRAR';
    }
}

document.getElementById('buscador').addEventListener('input', function() {
    const q = this.value.toLowerCase();
    document.querySelectorAll('.producto-card').forEach(card => {
        card.style.display = card.dataset.nombre.toLowerCase().includes(q) ? 'block' : 'none';
    });
});

// Intersection Observer — carga imagenes 300px antes de que sean visibles
const observer = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
        if (entry.isIntersecting) {
            const img = entry.target;
            img.src   = img.dataset.src;
            img.onload = () => {
                img.classList.add('loaded');
                const ph = document.getElementById('ph-' + img.dataset.id);
                if (ph) ph.classList.add('oculto');
            };
            observer.unobserve(img);
        }
    });
}, {
    root: document.getElementById('panel-scroll'),
    rootMargin: '300px 0px',
    threshold: 0
});

document.querySelectorAll('.lazy-img').forEach(img => observer.observe(img));

if ('serviceWorker' in navigator) {
    navigator.serviceWorker.register('/sw.js');
}
</script>
@endsection