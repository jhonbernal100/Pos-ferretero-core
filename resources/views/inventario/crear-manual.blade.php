@extends('layouts.pos')

@section('titulo', 'Crear producto manual')

@section('contenido')
<div style="max-width:600px;margin:24px auto;padding:0 16px;">
    <div style="background:#fff;border-radius:12px;padding:24px;">
        <h2 style="font-size:20px;margin-bottom:20px;">➕ Nuevo producto manual</h2>

        <div id="mensaje" style="display:none;padding:10px;border-radius:8px;margin-bottom:12px;font-size:14px;"></div>

        <div style="margin-bottom:14px;">
            <label style="display:block;font-size:13px;color:#555;margin-bottom:4px;">Foto del producto (opcional)</label>
            <input type="file" id="f-foto" accept="image/*"
                style="width:100%;padding:10px;font-size:14px;border:2px solid #ddd;border-radius:8px;">
            <img id="preview-foto" src="" alt=""
                style="display:none;width:100%;max-height:200px;object-fit:contain;border-radius:8px;margin-top:8px;border:1px solid #eee;">
        </div>

        <div style="margin-bottom:14px;">
            <label style="display:block;font-size:13px;color:#555;margin-bottom:4px;">Nombre *</label>
            <input type="text" id="f-nombre" placeholder="Ej: Puntilla 2 pulgadas galvanizada"
                style="width:100%;padding:12px;font-size:16px;border:2px solid #ddd;border-radius:8px;">
        </div>

        <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;margin-bottom:14px;">
            <div>
                <label style="display:block;font-size:13px;color:#555;margin-bottom:4px;">Marca</label>
                <input type="text" id="f-marca" placeholder="Ej: Pernos SA"
                    style="width:100%;padding:12px;font-size:16px;border:2px solid #ddd;border-radius:8px;">
            </div>
            <div>
                <label style="display:block;font-size:13px;color:#555;margin-bottom:4px;">Referencia</label>
                <input type="text" id="f-referencia"
                    style="width:100%;padding:12px;font-size:16px;border:2px solid #ddd;border-radius:8px;">
            </div>
        </div>

        <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;margin-bottom:14px;">
            <div>
                <label style="display:block;font-size:13px;color:#555;margin-bottom:4px;">Categoría</label>
                <select id="f-categoria"
                    style="width:100%;padding:12px;font-size:16px;border:2px solid #ddd;border-radius:8px;">
                    <option value="Tornillería">Tornillería</option>
                    <option value="Herramientas">Herramientas</option>
                    <option value="Construcción">Construcción</option>
                    <option value="Eléctrico">Eléctrico</option>
                    <option value="Plomería">Plomería</option>
                    <option value="Pintura">Pintura</option>
                    <option value="Seguridad">Seguridad</option>
                    <option value="General">General</option>
                </select>
            </div>
            <div>
                <label style="display:block;font-size:13px;color:#555;margin-bottom:4px;">Unidad</label>
                <select id="f-unidad"
                    style="width:100%;padding:12px;font-size:16px;border:2px solid #ddd;border-radius:8px;">
                    <option value="unidad">Unidad</option>
                    <option value="metro">Metro</option>
                    <option value="kilo">Kilo</option>
                    <option value="litro">Litro</option>
                    <option value="bolsa">Bolsa</option>
                    <option value="caja">Caja</option>
                </select>
            </div>
        </div>

        <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;margin-bottom:14px;">
            <div>
                <label style="display:block;font-size:13px;color:#555;margin-bottom:4px;">Precio costo (COP) *</label>
                <input type="number" id="f-precio-compra" placeholder="0" min="0"
                    style="width:100%;padding:12px;font-size:16px;border:2px solid #ddd;border-radius:8px;">
            </div>
            <div>
                <label style="display:block;font-size:13px;color:#555;margin-bottom:4px;">Precio venta (COP) *</label>
                <input type="number" id="f-precio-venta" placeholder="0" min="0"
                    style="width:100%;padding:12px;font-size:16px;border:2px solid #ddd;border-radius:8px;">
            </div>
        </div>

        <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;margin-bottom:14px;">
            <div>
                <label style="display:block;font-size:13px;color:#555;margin-bottom:4px;">Cantidad *</label>
                <input type="number" id="f-stock" placeholder="0" min="0"
                    style="width:100%;padding:12px;font-size:16px;border:2px solid #ddd;border-radius:8px;">
            </div>
            <div>
                <label style="display:block;font-size:13px;color:#555;margin-bottom:4px;">Stock mínimo</label>
                <input type="number" id="f-stock-minimo" value="5" min="0"
                    style="width:100%;padding:12px;font-size:16px;border:2px solid #ddd;border-radius:8px;">
            </div>
        </div>

        <div style="margin-bottom:20px;">
            <label style="display:block;font-size:13px;color:#555;margin-bottom:4px;">Proveedor</label>
            <select id="f-proveedor"
                style="width:100%;padding:12px;font-size:16px;border:2px solid #ddd;border-radius:8px;">
                <option value="">Sin proveedor</option>
                @foreach($proveedores as $proveedor)
                <option value="{{ $proveedor->id }}">{{ $proveedor->nombre }}</option>
                @endforeach
            </select>
        </div>

        <button onclick="guardar()"
            style="width:100%;padding:16px;background:#000;color:#fff;border:none;border-radius:10px;font-size:18px;font-weight:bold;cursor:pointer;">
            💾 Guardar producto
        </button>

        <a href="/inventario"
            style="display:block;text-align:center;margin-top:12px;color:#555;font-size:14px;">
            ← Volver al inventario
        </a>
    </div>
</div>

<script>
let fotoBase64 = null;

document.getElementById('f-foto').addEventListener('change', function() {
    const file = this.files[0];
    if (!file) return;
    const reader = new FileReader();
    reader.onload = e => {
        fotoBase64 = e.target.result.split(',')[1];
        const preview = document.getElementById('preview-foto');
        preview.src = e.target.result;
        preview.style.display = 'block';
    };
    reader.readAsDataURL(file);
});

async function guardar() {
    const nombre = document.getElementById('f-nombre').value;
    if (!nombre) { alert('El nombre es obligatorio'); return; }

    const precioVenta = parseInt(document.getElementById('f-precio-venta').value) || 0;
    if (!precioVenta) { alert('El precio de venta es obligatorio'); return; }

    const stock = parseInt(document.getElementById('f-stock').value) || 0;
    if (!stock) { alert('La cantidad es obligatoria'); return; }

    const payload = {
        nombre:        nombre,
        marca:         document.getElementById('f-marca').value,
        referencia:    document.getElementById('f-referencia').value,
        categoria:     document.getElementById('f-categoria').value,
        unidad:        document.getElementById('f-unidad').value,
        precio_compra: parseInt(document.getElementById('f-precio-compra').value) || 0,
        precio_venta:  precioVenta,
        stock:         stock,
        stock_minimo:  parseInt(document.getElementById('f-stock-minimo').value) || 5,
        foto_base64:   fotoBase64,
    };

    const res = await fetch('/inventario/guardar', {
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
    msg.textContent      = data.mensaje;

    if (data.success) {
        setTimeout(() => window.location.href = '/inventario', 1500);
    }
}
</script>
@endsection