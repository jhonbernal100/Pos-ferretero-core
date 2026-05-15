<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Ticket #{{ str_pad($venta->id, 6, '0', STR_PAD_LEFT) }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'Courier New', Courier, monospace;
            font-size: 13px;
            width: 80mm;
            margin: 0 auto;
            color: #000;
            background: #fff;
        }

        .copia {
            width: 80mm;
            padding: 4mm 3mm;
            page-break-after: always;
        }

        .copia:last-child {
            page-break-after: avoid;
        }

        .centro { text-align: center; }
        .derecha { text-align: right; }
        .negrita { font-weight: bold; }
        .grande { font-size: 15px; }
        .mayus { text-transform: uppercase; }

        .nombre-ferreteria {
            font-size: 16px;
            font-weight: bold;
            text-align: center;
            text-transform: uppercase;
            margin-bottom: 1mm;
            letter-spacing: 0.5px;
        }

        .nit {
            font-size: 14px;
            font-weight: bold;
            text-align: center;
            margin-bottom: 1mm;
        }

        .info-header {
            font-size: 12px;
            text-align: center;
            margin-bottom: 0.5mm;
        }

        .divider {
            border: none;
            border-top: 1px dashed #000;
            margin: 2mm 0;
        }

        .divider-solid {
            border: none;
            border-top: 1px solid #000;
            margin: 2mm 0;
        }

        .fila {
            display: flex;
            justify-content: space-between;
            font-size: 13px;
            margin-bottom: 0.8mm;
        }

        .fila .label { font-weight: bold; }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        .th-cant  { width: 8mm;  text-align: left;  font-size: 12px; font-weight: bold; padding-bottom: 1mm; }
        .th-desc  { width: 42mm; text-align: left;  font-size: 12px; font-weight: bold; padding-bottom: 1mm; }
        .th-prec  { width: 15mm; text-align: right; font-size: 12px; font-weight: bold; padding-bottom: 1mm; }
        .th-total { width: 15mm; text-align: right; font-size: 12px; font-weight: bold; padding-bottom: 1mm; }

        .td-cant  { width: 8mm;  text-align: left;  font-size: 13px; padding: 0.8mm 0; vertical-align: top; }
        .td-desc  { width: 42mm; text-align: left;  font-size: 13px; padding: 0.8mm 0; vertical-align: top; word-break: break-word; }
        .td-prec  { width: 15mm; text-align: right; font-size: 13px; padding: 0.8mm 0; vertical-align: top; }
        .td-total { width: 15mm; text-align: right; font-size: 13px; padding: 0.8mm 0; vertical-align: top; }

        .total-grande {
            font-size: 16px;
            font-weight: bold;
            text-align: center;
            border: 2px solid #000;
            padding: 2mm;
            margin: 2mm 0;
            letter-spacing: 1px;
        }

        .cambio-box {
            font-size: 16px;
            font-weight: bold;
            text-align: center;
            background: #000;
            color: #fff;
            padding: 2mm;
            margin: 2mm 0;
        }

        .footer {
            text-align: center;
            font-size: 12px;
            margin-top: 2mm;
            line-height: 1.6;
        }

        .tipo-copia {
            font-size: 12px;
            font-weight: bold;
            text-align: center;
            border: 1px solid #000;
            padding: 1mm;
            margin-bottom: 2mm;
            letter-spacing: 2px;
        }

        .no-print {
            display: block;
            text-align: center;
            margin: 5mm auto;
        }

        @media print {
            .no-print { display: none !important; }
            body { width: 80mm; }
            @page { size: 80mm auto; margin: 0; }
        }
    </style>
</head>
<body>

@for($copia = 1; $copia <= 2; $copia++)
<div class="copia">

    {{-- Tipo de copia --}}
    <div class="tipo-copia">
        {{ $copia === 1 ? '*** COPIA CLIENTE ***' : '*** COPIA FERRETERÍA ***' }}
    </div>

    {{-- Encabezado --}}
    <div class="nombre-ferreteria">{{ $venta->tenant->nombre }}</div>
    <div class="nit">NIT: {{ $venta->tenant->nit }}</div>
    @if($venta->tenant->direccion)
    <div class="info-header">{{ $venta->tenant->direccion }}</div>
    @endif
    @if($venta->tenant->telefono)
    <div class="info-header">TEL: {{ $venta->tenant->telefono }}</div>
    @endif
    @if($venta->tenant->ciudad)
    <div class="info-header">{{ strtoupper($venta->tenant->ciudad) }}</div>
    @endif

    <div class="divider"></div>

    {{-- Info ticket --}}
    <div class="fila">
        <span class="label">TICKET #:</span>
        <span class="negrita">{{ str_pad($venta->id, 6, '0', STR_PAD_LEFT) }}</span>
    </div>
    <div class="fila">
        <span class="label">FECHA:</span>
        <span>{{ $venta->created_at->format('d/m/Y') }}</span>
    </div>
    <div class="fila">
        <span class="label">HORA:</span>
        <span>{{ $venta->created_at->format('H:i:s') }}</span>
    </div>
    <div class="fila">
        <span class="label">CAJERO:</span>
        <span>{{ strtoupper(auth()->user()->name ?? 'SISTEMA') }}</span>
    </div>
    <div class="fila">
        <span class="label">CLIENTE:</span>
        <span>{{ strtoupper($venta->cliente->nombre ?? 'CONSUMIDOR FINAL') }}</span>
    </div>
    @if($venta->cliente?->numero_documento)
    <div class="fila">
        <span class="label">{{ $venta->cliente->tipo_documento }}:</span>
        <span>{{ $venta->cliente->numero_documento }}</span>
    </div>
    @endif
    <div class="fila">
        <span class="label">PAGO:</span>
        <span>{{ strtoupper($venta->metodo_pago) }}</span>
    </div>

    <div class="divider-solid"></div>

    {{-- Detalle productos --}}
    <table>
        <thead>
            <tr>
                <th class="th-cant">Uds</th>
                <th class="th-desc">DESCRIPCION</th>
                <th class="th-prec">PRECIO</th>
                <th class="th-total">TOTAL</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td colspan="4"><hr style="border-top:1px dashed #000;margin:1mm 0;"></td>
            </tr>
            @foreach($venta->detalles as $detalle)
            <tr>
                <td class="td-cant">{{ $detalle->cantidad }}</td>
                <td class="td-desc">{{ strtoupper($detalle->nombre_producto) }}</td>
                <td class="td-prec">{{ number_format($detalle->precio_unitario, 0, ',', '.') }}</td>
                <td class="td-total">{{ number_format($detalle->subtotal, 0, ',', '.') }}</td>
            </tr>
            @endforeach
            <tr>
                <td colspan="4"><hr style="border-top:1px dashed #000;margin:1mm 0;"></td>
            </tr>
        </tbody>
    </table>

    {{-- Totales --}}
    @if($venta->descuento > 0)
    <div class="fila">
        <span>SUBTOTAL</span>
        <span>$ {{ number_format($venta->subtotal, 0, ',', '.') }}</span>
    </div>
    <div class="fila">
        <span>DESCUENTO</span>
        <span>- $ {{ number_format($venta->descuento, 0, ',', '.') }}</span>
    </div>
    @endif

    <div class="total-grande">
        TOTAL: $ {{ number_format($venta->total, 0, ',', '.') }}
    </div>

    <div class="fila">
        <span class="label">PAGADO:</span>
        <span>$ {{ number_format($venta->monto_pagado, 0, ',', '.') }}</span>
    </div>

    @if($venta->cambio > 0)
    <div class="cambio-box">
        CAMBIO: $ {{ number_format($venta->cambio, 0, ',', '.') }}
    </div>
    @endif

    <div class="divider"></div>

    {{-- Footer --}}
    <div class="footer">
        <p>*** GRACIAS POR SU COMPRA ***</p>
        <p>Conserve este ticket</p>
        <p style="margin-top:1mm;font-size:10px;">
            Sistema POS — Avanzas Digital
        </p>
    </div>

</div>
@endfor

{{-- Botón imprimir --}}
<div class="no-print">
    <button onclick="window.print()"
        style="padding:10px 20px;font-size:16px;cursor:pointer;background:#000;color:#fff;border:none;border-radius:6px;margin:10px;">
        🖨 Imprimir ticket
    </button>
</div>

<script>
    window.onload = function() {
        setTimeout(function() {
            window.print();
        }, 600);
    };
</script>

</body>
</html>