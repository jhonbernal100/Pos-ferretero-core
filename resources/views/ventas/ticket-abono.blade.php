<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Abono #{{ str_pad($venta->id, 6, '0', STR_PAD_LEFT) }}</title>
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
        .negrita { font-weight: bold; }
        .divider { border-top: 1px dashed #000; margin: 2mm 0; }
        .grande { font-size: 16px; font-weight: bold; text-align: center; }
        .footer { text-align: center; font-size: 10px; margin-top: 3mm; }
        .btn-imprimir {
            display: block;
            width: 100%;
            padding: 10px;
            background: #000;
            color: #fff;
            border: none;
            border-radius: 6px;
            font-size: 16px;
            cursor: pointer;
            margin-top: 8mm;
        }
        @media print {
            .no-print { display: none; }
            @page { size: 80mm auto; margin: 0; }
        }
    </style>
</head>
<body>

    <div class="centro negrita" style="font-size:14px;">
        {{ $venta->tenant->nombre }}
    </div>
    <div class="centro">NIT: {{ $venta->tenant->nit }}</div>
    <div class="centro">Tel: {{ $venta->tenant->telefono }}</div>

    <div class="divider"></div>

    <div class="centro negrita" style="font-size:13px;">COMPROBANTE DE ABONO</div>

    <div class="divider"></div>

    <div>Abono #: <span class="negrita">{{ str_pad($venta->id, 6, '0', STR_PAD_LEFT) }}</span></div>
    <div>Fecha: {{ $venta->created_at->format('d/m/Y H:i') }}</div>
    <div>Cliente: <span class="negrita">{{ $venta->cliente->nombre }}</span></div>
    @if($venta->cliente->numero_documento)
    <div>Doc: {{ $venta->cliente->tipo_documento }} {{ $venta->cliente->numero_documento }}</div>
    @endif
    <div>Forma de pago: {{ ucfirst($venta->metodo_pago) }}</div>

    <div class="divider"></div>

    <div class="grande">
        ABONO: $ {{ number_format($venta->total, 0, ',', '.') }}
    </div>

    <div class="divider"></div>

    @if($credito)
    <div>Saldo anterior: $ {{ number_format($saldo_anterior, 0, ',', '.') }}</div>
    <div>Abono aplicado: $ {{ number_format($venta->total, 0, ',', '.') }}</div>
    <div class="negrita">Saldo pendiente: $ {{ number_format($credito->saldo_usado, 0, ',', '.') }}</div>
    <div class="negrita">Cupo disponible: $ {{ number_format($credito->saldoDisponible(), 0, ',', '.') }}</div>
    @endif

    <div class="divider"></div>

    <div class="footer">
        <p>Este comprobante es válido como</p>
        <p>constancia de pago parcial</p>
        <p>{{ $venta->tenant->ciudad }}</p>
        <p style="margin-top:2mm;">Sistema POS — Avanzas Digital</p>
    </div>

    <div class="no-print">
        <button class="btn-imprimir" onclick="window.print()">
            Imprimir comprobante
        </button>
    </div>

    <script>
        window.onload = function() {
            setTimeout(function() { window.print(); }, 500);
        };
    </script>

</body>
</html>