@extends('layouts.pos')

@section('titulo', 'Nuevo Proveedor')

@section('contenido')
<div style="max-width:600px;margin:24px auto;padding:0 16px;">
    <div style="background:#fff;border-radius:12px;padding:24px;">
        <h2 style="font-size:20px;margin-bottom:20px;">🚚 Nuevo proveedor</h2>

        <div id="mensaje" style="display:none;padding:10px;border-radius:8px;margin-bottom:12px;font-size:14px;"></div>

        <div style="margin-bottom:14px;">
            <label style="display:block;font-size:13px;color:#555;margin-bottom:4px;">Nombre *</label>
            <input type="text" id="f-nombre" placeholder="Nombre del proveedor"
                style="width:100%;padding:12px;font-size:16px;border:2px solid #ddd;border-radius:8px;">
        </div>

        <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;margin-bottom:14px;">
            <div>
                <label style="display:block;font-size:13px;color:#555;margin-bottom:4px;">NIT</label>
                <input type="text" id="f-nit" placeholder="900123456-1"
                    style="width:100%;padding:12px;font-size:16px;border:2px solid #ddd;border-radius:8px;">
            </div>
            <div>
                <label style="display:block;font-size:13px;color:#555;margin-bottom:4px;">Teléfono</label>
                <input type="text" id="f-telefono" placeholder="+57 300 000 0000"
                    style="width:100%;padding:12px;font-size:16px;border:2px solid #ddd;border-radius:8px;">
            </div>
        </div>

        <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;margin-bottom:14px;">
            <div>
                <label style="display:block;font-size:13px;color:#555;margin-bottom:4px;">Contacto</label>
                <input type="text" id="f-contacto" placeholder="Nombre del contacto"
                    style="width:100%;padding:12px;font-size:16px;border:2px solid #ddd;border-radius:8px;">
            </div>
            <div>
                <label style="display:block;font-size:13px;color:#555;margin-bottom:4px;">Ciudad</label>
                <input type="text" id="f-ciudad" placeholder="Bogotá"
                    style="width:100%;padding:12px;font-size:16px;border:2px solid #ddd;border-radius:8px;">
            </div>
        </div>

        <div style="margin-bottom:14px;">
            <label style="display:block;font-size:13px;color:#555;margin-bottom:4px;">Email</label>
            <input type="email" id="f-email" placeholder="proveedor@email.com"
                style="width:100%;padding:12px;font-size:16px;border:2px solid #ddd;border-radius:8px;">
        </div>

        <div style="margin-bottom:20px;">
            <label style="display:block;font-size:13px;color:#555;margin-bottom:4px;">Notas</label>
            <textarea id="f-notas" placeholder="Información adicional..."
                style="width:100%;padding:12px;font-size:16px;border:2px solid #ddd;border-radius:8px;height:80px;resize:none;"></textarea>
        </div>

        <button onclick="guardar()"
            style="width:100%;padding:16px;background:#000;color:#fff;border:none;border-radius:10px;font-size:18px;font-weight:bold;cursor:pointer;">
            💾 Guardar proveedor
        </button>
    </div>
</div>

<script>
async function guardar() {
    const nombre = document.getElementById('f-nombre').value;
    if (!nombre) { alert('El nombre es obligatorio'); return; }

    const res = await fetch('/proveedores', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
        },
        body: JSON.stringify({
            nombre:   nombre,
            nit:      document.getElementById('f-nit').value,
            telefono: document.getElementById('f-telefono').value,
            contacto: document.getElementById('f-contacto').value,
            ciudad:   document.getElementById('f-ciudad').value,
            email:    document.getElementById('f-email').value,
            notas:    document.getElementById('f-notas').value,
        }),
    });

    const data = await res.json();
    const msg  = document.getElementById('mensaje');
    msg.style.display    = 'block';
    msg.style.background = data.success ? '#d4edda' : '#f8d7da';
    msg.style.color      = data.success ? '#155724' : '#721c24';
    msg.textContent      = data.mensaje;

    if (data.success) {
        setTimeout(() => window.location.href = '/proveedores', 1500);
    }
}
</script>
@endsection