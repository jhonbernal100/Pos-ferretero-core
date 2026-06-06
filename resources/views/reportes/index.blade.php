@extends('layouts.pos')

@section('titulo', 'Reportes')

@section('contenido')
<div style="padding:16px;max-width:1000px;margin:0 auto;">
    <h1 style="font-size:22px;margin-bottom:20px;">📊 Reportes</h1>

    <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(280px,1fr));gap:16px;">

        {{-- Inventario --}}
        <div style="background:#fff;border-radius:12px;padding:20px;border-left:4px solid #99CF8E;">
            <div style="font-size:32px;margin-bottom:8px;">📦</div>
            <h3 style="font-size:16px;margin-bottom:6px;">Inventario completo</h3>
            <p style="font-size:13px;color:#888;margin-bottom:16px;">
                Lista de todos los productos con precios, stock y valor total.
            </p>
            <a href="/reportes/inventario" target="_blank"
               style="display:block;padding:10px;background:#000;color:#fff;text-align:center;border-radius:8px;text-decoration:none;font-size:14px;">
                📄 Generar PDF
            </a>
        </div>

        {{-- Stock bajo --}}
        <div style="background:#fff;border-radius:12px;padding:20px;border-left:4px solid #ffc107;">
            <div style="font-size:32px;margin-bottom:8px;">⚠️</div>
            <h3 style="font-size:16px;margin-bottom:6px;">Alertas de stock</h3>
            <p style="font-size:13px;color:#888;margin-bottom:16px;">
                Productos agotados o con stock por debajo del mínimo.
            </p>
            <a href="/reportes/stock-bajo" target="_blank"
               style="display:block;padding:10px;background:#000;color:#fff;text-align:center;border-radius:8px;text-decoration:none;font-size:14px;">
                📄 Generar PDF
            </a>
        </div>

        {{-- Ventas del día --}}
        <div style="background:#fff;border-radius:12px;padding:20px;border-left:4px solid #4285F4;">
            <div style="font-size:32px;margin-bottom:8px;">🗓</div>
            <h3 style="font-size:16px;margin-bottom:6px;">Ventas del día</h3>
            <p style="font-size:13px;color:#888;margin-bottom:16px;">
                Resumen de todas las ventas realizadas hoy.
            </p>
            <a href="/reportes/ventas-dia" target="_blank"
               style="display:block;padding:10px;background:#000;color:#fff;text-align:center;border-radius:8px;text-decoration:none;font-size:14px;">
                📄 Generar PDF
            </a>
        </div>

        {{-- Ventas semana --}}
        <div style="background:#fff;border-radius:12px;padding:20px;border-left:4px solid #34A853;">
            <div style="font-size:32px;margin-bottom:8px;">📅</div>
            <h3 style="font-size:16px;margin-bottom:6px;">Ventas de la semana</h3>
            <p style="font-size:13px;color:#888;margin-bottom:16px;">
                Resumen de ventas de la semana actual.
            </p>
            <a href="/reportes/ventas-semana" target="_blank"
               style="display:block;padding:10px;background:#000;color:#fff;text-align:center;border-radius:8px;text-decoration:none;font-size:14px;">
                📄 Generar PDF
            </a>
        </div>

        {{-- Ventas mes --}}
        <div style="background:#fff;border-radius:12px;padding:20px;border-left:4px solid #EA4335;">
            <div style="font-size:32px;margin-bottom:8px;">📈</div>
            <h3 style="font-size:16px;margin-bottom:6px;">Ventas del mes</h3>
            <p style="font-size:13px;color:#888;margin-bottom:16px;">
                Resumen mensual con productos más vendidos.
            </p>
            <div style="display:flex;gap:8px;margin-bottom:8px;">
                <select id="sel-mes"
                    style="flex:1;padding:8px;font-size:14px;border:1px solid #ddd;border-radius:6px;">
                    @foreach(range(1,12) as $m)
                    <option value="{{ $m }}" {{ $m == now()->month ? 'selected' : '' }}>
                        {{ Carbon\Carbon::create()->month($m)->locale('es')->monthName }}
                    </option>
                    @endforeach
                </select>
                <select id="sel-anio"
                    style="width:90px;padding:8px;font-size:14px;border:1px solid #ddd;border-radius:6px;">
                    @foreach(range(now()->year, now()->year - 2) as $a)
                    <option value="{{ $a }}">{{ $a }}</option>
                    @endforeach
                </select>
            </div>
            <button onclick="generarMes()"
               style="width:100%;padding:10px;background:#000;color:#fff;border:none;border-radius:8px;font-size:14px;cursor:pointer;">
                📄 Generar PDF
            </button>
        </div>

        {{-- Cartera creditos --}}
<div style="background:#fff;border-radius:12px;padding:20px;border-left:4px solid #EA4335;">
    <div style="font-size:32px;margin-bottom:8px;">💳</div>
    <h3 style="font-size:16px;margin-bottom:6px;">Cartera de creditos</h3>
    <p style="font-size:13px;color:#888;margin-bottom:16px;">
        Clientes con saldo pendiente, topes y porcentaje de uso del cupo.
    </p>
    <a href="/reportes/creditos" target="_blank"
       style="display:block;padding:10px;background:#000;color:#fff;text-align:center;border-radius:8px;text-decoration:none;font-size:14px;">
        Generar PDF
    </a>
</div>

{{-- Estado financiero --}}
<div style="background:#fff;border-radius:12px;padding:20px;border-left:4px solid #000;">
    <div style="font-size:32px;margin-bottom:8px;">📊</div>
    <h3 style="font-size:16px;margin-bottom:6px;">Estado financiero</h3>
    <p style="font-size:13px;color:#888;margin-bottom:16px;">
        P&G, cuentas por cobrar, cartera por antiguedad y valor de inventario.
    </p>
    <div style="display:flex;gap:8px;margin-bottom:8px;">
        <select id="sel-mes-fin"
            style="flex:1;padding:8px;font-size:14px;border:1px solid #ddd;border-radius:6px;">
            @foreach(range(1,12) as $m)
            <option value="{{ $m }}" {{ $m == now()->month ? 'selected' : '' }}>
                {{ Carbon\Carbon::create()->month($m)->locale('es')->monthName }}
            </option>
            @endforeach
        </select>
        <select id="sel-anio-fin"
            style="width:90px;padding:8px;font-size:14px;border:1px solid #ddd;border-radius:6px;">
            @foreach(range(now()->year, now()->year - 2) as $a)
            <option value="{{ $a }}">{{ $a }}</option>
            @endforeach
        </select>
    </div>
    <button onclick="generarFinanciero()"
       style="width:100%;padding:10px;background:#000;color:#fff;border:none;border-radius:8px;font-size:14px;cursor:pointer;">
        Generar PDF
    </button>
</div>


        {{-- Kardex --}}
        <div style="background:#fff;border-radius:12px;padding:20px;border-left:4px solid #CEC8BF;">
            <div style="font-size:32px;margin-bottom:8px;">📋</div>
            <h3 style="font-size:16px;margin-bottom:6px;">Kardex de producto</h3>
            <p style="font-size:13px;color:#888;margin-bottom:16px;">
                Movimientos de entrada y salida de un producto.
            </p>
            <select id="sel-producto"
                style="width:100%;padding:8px;font-size:14px;border:1px solid #ddd;border-radius:6px;margin-bottom:8px;">
                <option value="">Seleccionar producto</option>
                @foreach(\App\Models\Producto::where('activo', true)->orderBy('nombre')->get() as $p)
                <option value="{{ $p->id }}">{{ $p->nombre }}</option>
                @endforeach
            </select>
            <button onclick="generarKardex()"
               style="width:100%;padding:10px;background:#000;color:#fff;border:none;border-radius:8px;font-size:14px;cursor:pointer;">
                📄 Generar PDF
            </button>
        </div>

    </div>
</div>



<script>
function generarMes() {
    const mes  = document.getElementById('sel-mes').value;
    const anio = document.getElementById('sel-anio').value;
    window.open(`/reportes/ventas-mes?mes=${mes}&anio=${anio}`, '_blank');
}

function generarKardex() {
    const id = document.getElementById('sel-producto').value;
    if (!id) { alert('Selecciona un producto'); return; }
    window.open(`/reportes/kardex?producto_id=${id}`, '_blank');
}

function generarFinanciero() {
    const mes  = document.getElementById('sel-mes-fin').value;
    const anio = document.getElementById('sel-anio-fin').value;
    window.open(`/reportes/estado-financiero?mes=${mes}&anio=${anio}`, '_blank');
}
</script>
@endsection