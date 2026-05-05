<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Ticket #{{ $venta->id }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'Courier New', monospace;
            font-size: 11px;
            width: 80mm;
            margin: 0 auto;
            padding: 4mm;
            color: #000;
        }

        .centro { text-align: center; }
        .derecha { text-align: right; }
        .negrita { font-weight: bold; }

        .nombre-ferreteria {
            font-size: 14px;
            font-weight: bold;
            text-align: center;
            margin-bottom: 2mm;
        }

        .divider {
            border-top: 1px dashed #000;
            margin: 2mm 0;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th {
            font-weight: bold;
            text-align: left;
            font-size: 10px;
            padding-bottom: 1mm;
        }

        td {
            font-size: 10px;
            padding: 0.5mm 0;
            vertical-align: top;
        }

        .td-cant  { width: 8mm; }
        .td-desc  { width: 42mm; }
        .td-precio { width: 28mm; text-align: right; }

        .total-row td {
            font-size: 12px;
            font-weight: bold;
            padding-top: 1mm;
        }

        .cambio-box {
            background: #000;
            color: #fff;
            text-align: center;
            padding: 2mm;
            font-size: 13px;
            font-weight: bold;
            margin: 2mm 0;
        }

        .footer {
            text-align: center;
            font-size: 10px;
            margin-top: 3mm;
        }

        @media print {
            body { width: 80mm; }
            .no-print { display: none; }

            @page {
                size: 80mm auto;
                margin: 0;
            }
        }
    </style>
</head>
<body>

    {{-- Encabezado ferretería --}}
    <div class="nombre-ferreteria">{{ $venta->tenant->nombre }}</div>
    <div class="centro">{{ $venta->tenant->direccion }}</div>
    <div class="centro">Tel: {{ $venta->tenant->telefono }}</div>
    <div class="centro">NIT: {{ $venta->tenant->nit }}</div>

    <div class="divider"></div>

    {{-- Info del ticket --}}
    <div>Ticket #: <span class="negrita">{{ str_pad($venta->id, 6, '0', STR_PAD_LEFT) }}</span></div>
    <div>Fecha: {{ $venta->created_at->format('d/m/Y H:i') }}</div>
    @if($venta->cliente)
    <div>Cliente: {{ $venta->cliente->nombre }}</div>
    @else
    <div>Cliente: Consumidor final</div>
    @endif
    <div>Pago: {{ ucfirst($venta->metodo_pago) }}</div>

    <div class="divider"></div>

    {{-- Detalle productos --}}
    <table>
        <thead>
            <tr>
                <th class="td-cant">Cant</th>
                <th class="td-desc">Descripción</th>
                <th class="td-precio">Valor</th>
            </tr>
        </thead>
        <tbody>
            @foreach($venta->detalles as $detalle)
            <tr>
                <td class="td-cant">{{ $detalle->cantidad }}</td>
                <td class="td-desc">{{ $detalle->nombre_producto }}</td>
                <td class="td-precio">{{ number_format($detalle->subtotal, 0, ',', '.') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="divider"></div>

    {{-- Totales --}}
    <table>
        @if($venta->descuento > 0)
        <tr>
            <td>Subtotal</td>
            <td class="derecha">$ {{ number_format($venta->subtotal, 0, ',', '.') }}</td>
        </tr>
        <tr>
            <td>Descuento</td>
            <td class="derecha">- $ {{ number_format($venta->descuento, 0, ',', '.') }}</td>
        </tr>
        @endif
        <tr class="total-row">
            <td>TOTAL</td>
            <td class="derecha">$ {{ number_format($venta->total, 0, ',', '.') }}</td>
        </tr>
        <tr>
            <td>Pagado</td>
            <td class="derecha">$ {{ number_format($venta->monto_pagado, 0, ',', '.') }}</td>
        </tr>
    </table>

    {{-- Cambio --}}
    @if($venta->cambio > 0)
    <div class="cambio-box">
        CAMBIO: $ {{ number_format($venta->cambio, 0, ',', '.') }}
    </div>
    @endif

    <div class="divider"></div>

    {{-- Footer --}}
    <div class="footer">
        <p>¡Gracias por su compra!</p>
        <p>Conserve este ticket</p>
        <p>{{ $venta->tenant->ciudad }}</p>
    </div>

    {{-- Botón imprimir solo en pantalla --}}
    <div class="no-print" style="text-align:center; margin-top:5mm;">
        <button onclick="window.print()"
            style="padding:3mm 8mm; font-size:14px; cursor:pointer; background:#000; color:#fff; border:none; border-radius:4px;">
            Imprimir ticket
        </button>
    </div>

    <script>
        // Imprimir automáticamente al abrir la página
        window.onload = function() {
            // Pequeño delay para que cargue el CSS
            setTimeout(function() {
                window.print();
            }, 500);
        };
    </script>

</body>
</html>