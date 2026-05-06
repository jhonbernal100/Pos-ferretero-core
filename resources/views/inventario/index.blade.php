@extends('layouts.pos')

@section('titulo', 'Inventario')

@section('contenido')
<div style="padding:16px;max-width:1200px;margin:0 auto;">
    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:16px;">
        <h1 style="font-size:22px;">📦 Inventario</h1>
        <a href="/inventario/capturar"
           style="padding:10px 20px;background:#000;color:#fff;border-radius:8px;text-decoration:none;font-size:14px;">
            📷 Agregar producto
        </a>
    </div>

    <div style="margin-bottom:12px;">
        <input type="text" id="buscador" placeholder="Buscar producto..."
               style="width:100%;padding:12px;font-size:16px;border:2px solid #ddd;border-radius:8px;">
    </div>

    <div style="background:#fff;border-radius:12px;overflow:hidden;">
        <table style="width:100%;border-collapse:collapse;">
            <thead>
                <tr style="background:#000;color:#fff;">
                    <th style="padding:12px;text-align:left;font-size:13px;">Producto</th>
                    <th style="padding:12px;text-align:left;font-size:13px;">Categoría</th>
                    <th style="padding:12px;text-align:left;font-size:13px;">Precio venta</th>
                    <th style="padding:12px;text-align:center;font-size:13px;">Stock</th>
                    <th style="padding:12px;text-align:left;font-size:13px;">Estado</th>
                </tr>
            </thead>
            <tbody id="tabla-productos">
                @forelse($productos as $producto)
                <tr style="border-bottom:1px solid #eee;" class="fila-producto"
                    data-nombre="{{ strtolower($producto->nombre) }}">
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
                    <td style="padding:12px;font-size:12px;">
                        @if($producto->stock <= 0)
                            <span style="color:#721c24;font-weight:bold;">⛔ Sin stock</span>
                        @elseif($producto->stock <= $producto->stock_minimo)
                            <span style="color:#856404;font-weight:bold;">⚠️ Stock bajo</span>
                        @else
                            <span style="color:#155724;">✅ OK</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" style="padding:24px;text-align:center;color:#999;">
                        No hay productos. <a href="/inventario/capturar" style="color:#000;">Agrega el primero</a>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<script>
document.getElementById('buscador').addEventListener('input', function() {
    const q = this.value.toLowerCase();
    document.querySelectorAll('.fila-producto').forEach(fila => {
        fila.style.display = fila.dataset.nombre.includes(q) ? '' : 'none';
    });
});
</script>
@endsection