@extends('layouts.pos')

@section('titulo', 'Inventario')

@section('contenido')
<div style="padding:16px;max-width:1200px;margin:0 auto;">
    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:16px;flex-wrap:wrap;gap:8px;">
        <h1 style="font-size:22px;">📦 Inventario</h1>
        <div style="display:flex;gap:8px;">
            <a href="/inventario/crear-manual"
               style="padding:10px 16px;background:#fff;color:#000;border:2px solid #000;border-radius:8px;text-decoration:none;font-size:14px;font-weight:bold;">
                ➕ Crear manual
            </a>
            <a href="/inventario/capturar"
               style="padding:10px 16px;background:#000;color:#fff;border-radius:8px;text-decoration:none;font-size:14px;font-weight:bold;">
                📷 Capturar con foto
            </a>
        </div>
    </div>

    <div style="margin-bottom:12px;">
        <input type="text" id="buscador" placeholder="Buscar producto..."
               style="width:100%;padding:12px;font-size:16px;border:2px solid #ddd;border-radius:8px;">
    </div>

    <div style="background:#fff;border-radius:12px;overflow:hidden;">
        <table style="width:100%;border-collapse:collapse;">
            <thead>
                <tr style="background:#000;color:#fff;">
                    <th style="padding:12px;text-align:left;font-size:13px;width:70px;">Foto</th>
                    <th style="padding:12px;text-align:left;font-size:13px;">Producto</th>
                    <th style="padding:12px;text-align:left;font-size:13px;">Categoría</th>
                    <th style="padding:12px;text-align:left;font-size:13px;">Precio venta</th>
                    <th style="padding:12px;text-align:center;font-size:13px;">Stock</th>
                    <th style="padding:12px;text-align:center;font-size:13px;">Acciones</th>
                </tr>
            </thead>
            <tbody id="tabla-productos">
                @forelse($productos as $producto)
                <tr style="border-bottom:1px solid #eee;" class="fila-producto"
                    data-nombre="{{ strtolower($producto->nombre) }}"
                    id="fila-prod-{{ $producto->id }}">
                    <td style="padding:8px;">
                        @if($producto->foto)
                        <img src="{{ asset('storage/' . $producto->foto) }}"
                             onclick="verFoto('{{ asset('storage/' . $producto->foto) }}', '{{ $producto->nombre }}')"
                             style="width:52px;height:52px;object-fit:cover;border-radius:6px;border:1px solid #eee;cursor:pointer;"
                             title="Click para ampliar">
                        @else
                        <div style="width:52px;height:52px;background:#f5f5f5;border-radius:6px;display:flex;align-items:center;justify-content:center;font-size:24px;">
                            🔩
                        </div>
                        @endif
                    </td>
                    <td style="padding:12px;">
                        <div style="font-size:14px;font-weight:bold;">{{ $producto->nombre }}</div>
                        @if($producto->marca)
                        <div style="font-size:12px;color:#888;">{{ $producto->marca }}</div>
                        @endif
                    </td>
                    <td style="padding:12px;font-size:13px;">{{ $producto->categoria }}</td>
                    <td style="padding:12px;font-size:13px;">
                        $ {{ number_format($producto->precio_venta, 0, ',', '.') }}
                    </td>
                    <td style="padding:12px;text-align:center;">
                        <span style="padding:4px 12px;border-radius:12px;font-size:13px;font-weight:bold;
                            background:{{ $producto->stock <= 0 ? '#f8d7da' : ($producto->stock <= $producto->stock_minimo ? '#fff3cd' : '#d4edda') }};
                            color:{{ $producto->stock <= 0 ? '#721c24' : ($producto->stock <= $producto->stock_minimo ? '#856404' : '#155724') }};">
                            {{ $producto->stock }}
                        </span>
                    </td>
                    <td style="padding:12px;text-align:center;">
                        <div style="display:flex;gap:6px;justify-content:center;">
                            <a href="/inventario/{{ $producto->id }}/editar"
                               style="padding:6px 12px;background:#000;color:#fff;border-radius:6px;font-size:12px;text-decoration:none;">
                                ✏️ Editar
                            </a>
                            <button onclick="eliminarProducto({{ $producto->id }}, '{{ addslashes($producto->nombre) }}')"
                                style="padding:6px 12px;background:#c00;color:#fff;border:none;border-radius:6px;font-size:12px;cursor:pointer;">
                                🗑 Eliminar
                            </button>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" style="padding:24px;text-align:center;color:#999;">
                        No hay productos.
                        <a href="/inventario/crear-manual" style="color:#000;">Crea el primero manualmente</a>
                        o
                        <a href="/inventario/capturar" style="color:#000;">captura con foto</a>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- Modal foto ampliada --}}
<div id="modal-foto" onclick="cerrarModal()"
     style="display:none;position:fixed;top:0;left:0;width:100%;height:100%;background:rgba(0,0,0,0.85);z-index:9999;align-items:center;justify-content:center;flex-direction:column;">
    <img id="modal-img" src="" alt=""
         style="max-width:90%;max-height:80vh;object-fit:contain;border-radius:12px;box-shadow:0 8px 40px rgba(0,0,0,0.5);">
    <div id="modal-nombre"
         style="color:#fff;font-size:16px;margin-top:16px;font-weight:bold;"></div>
    <div style="color:#aaa;font-size:13px;margin-top:8px;">Toca para cerrar</div>
</div>

@section('scripts')
<script>
document.getElementById('buscador').addEventListener('input', function() {
    const q = this.value.toLowerCase();
    document.querySelectorAll('.fila-producto').forEach(fila => {
        fila.style.display = fila.dataset.nombre.includes(q) ? '' : 'none';
    });
});

function verFoto(url, nombre) {
    const modal = document.getElementById('modal-foto');
    document.getElementById('modal-img').src = url;
    document.getElementById('modal-nombre').textContent = nombre;
    modal.style.display = 'flex';
}

function cerrarModal() {
    document.getElementById('modal-foto').style.display = 'none';
}

async function eliminarProducto(id, nombre) {
    if (!confirm(`¿Eliminar "${nombre}" del inventario?\n\nEsta acción no se puede deshacer.`)) return;

    const res = await fetch(`/inventario/${id}/eliminar`, {
        method: 'DELETE',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
        },
    });

    const data = await res.json();
    if (data.success) {
        document.getElementById('fila-prod-' + id).remove();
        alert('✅ ' + data.mensaje);
    } else {
        alert('❌ Error: ' + data.mensaje);
    }
}
</script>
@endsection
@endsection