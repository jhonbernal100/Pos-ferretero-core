<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nueva Venta</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: Arial, sans-serif; background: #f0f0f0; }

        .layout { display: grid; grid-template-columns: 1fr 380px; height: 100vh; }

        /* Panel izquierdo - productos */
        .panel-productos {
            padding: 16px;
            overflow-y: auto;
        }

        .buscador {
            width: 100%;
            padding: 14px;
            font-size: 18px;
            border: 2px solid #000;
            border-radius: 8px;
            margin-bottom: 16px;
        }

        .grid-productos {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(160px, 1fr));
            gap: 12px;
        }

        .producto-card {
            background: #fff;
            border: 2px solid #ddd;
            border-radius: 10px;
            padding: 14px;
            cursor: pointer;
            transition: border-color .2s, transform .1s;
        }

        .producto-card:hover {
            border-color: #000;
            transform: scale(1.02);
        }

        .producto-card.sin-stock {
            opacity: .5;
            cursor: not-allowed;
        }

        .producto-card .nombre {
            font-size: 14px;
            font-weight: bold;
            margin-bottom: 6px;
            line-height: 1.3;
        }

        .producto-card .precio {
            font-size: 18px;
            font-weight: bold;
            color: #000;
        }

        .producto-card .stock {
            font-size: 11px;
            margin-top: 4px;
        }

        .stock-ok   { color: #155724; }
        .stock-bajo { color: #856404; }
        .stock-cero { color: #721c24; }

        /* Panel derecho - carrito */
        .panel-carrito {
            background: #fff;
            display: flex;
            flex-direction: column;
            border-left: 2px solid #ddd;
        }

        .carrito-header {
            padding: 16px;
            border-bottom: 2px solid #eee;
            font-size: 18px;
            font-weight: bold;
        }

        .carrito-items {
            flex: 1;
            overflow-y: auto;
            padding: 12px;
        }

        .carrito-item {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 10px 0;
            border-bottom: 1px solid #eee;
        }

        .carrito-item .item-nombre {
            flex: 1;
            font-size: 13px;
            font-weight: bold;
        }

        .carrito-item .item-precio {
            font-size: 13px;
            color: #555;
            min-width: 70px;
            text-align: right;
        }

        .btn-cant {
            width: 32px;
            height: 32px;
            border: 1px solid #ddd;
            background: #f5f5f5;
            border-radius: 6px;
            font-size: 18px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .cant-num {
            width: 28px;
            text-align: center;
            font-size: 15px;
            font-weight: bold;
        }

        .btn-quitar {
            background: none;
            border: none;
            color: #c00;
            font-size: 18px;
            cursor: pointer;
            padding: 0 4px;
        }

        /* Totales y cobro */
        .carrito-footer {
            padding: 16px;
            border-top: 2px solid #eee;
        }

        .fila-total {
            display: flex;
            justify-content: space-between;
            font-size: 15px;
            margin-bottom: 8px;
        }

        .fila-total.grande {
            font-size: 22px;
            font-weight: bold;
            margin: 12px 0;
        }

        .select-pago {
            width: 100%;
            padding: 12px;
            font-size: 16px;
            border: 2px solid #ddd;
            border-radius: 8px;
            margin-bottom: 10px;
        }

        .input-pago {
            width: 100%;
            padding: 12px;
            font-size: 20px;
            border: 2px solid #000;
            border-radius: 8px;
            margin-bottom: 10px;
            text-align: right;
        }

        .cambio-display {
            background: #000;
            color: #fff;
            text-align: center;
            padding: 10px;
            border-radius: 8px;
            font-size: 20px;
            font-weight: bold;
            margin-bottom: 10px;
            display: none;
        }

        .btn-cobrar {
            width: 100%;
            padding: 18px;
            background: #000;
            color: #fff;
            border: none;
            border-radius: 10px;
            font-size: 22px;
            font-weight: bold;
            cursor: pointer;
            letter-spacing: 1px;
        }

        .btn-cobrar:disabled {
            background: #aaa;
            cursor: not-allowed;
        }

        .btn-limpiar {
            width: 100%;
            padding: 10px;
            background: none;
            border: 1px solid #ddd;
            border-radius: 8px;
            font-size: 14px;
            cursor: pointer;
            margin-top: 8px;
            color: #c00;
        }

        /* Select cliente */
        .select-cliente {
            width: 100%;
            padding: 10px;
            font-size: 14px;
            border: 1px solid #ddd;
            border-radius: 8px;
            margin-bottom: 10px;
        }

        .empty-carrito {
            text-align: center;
            color: #aaa;
            margin-top: 40px;
            font-size: 15px;
        }
        /* PWA móvil */
@media (max-width: 768px) {
    .layout {
        grid-template-columns: 1fr;
        grid-template-rows: 1fr auto;
    }

    .panel-carrito {
        border-left: none;
        border-top: 2px solid #ddd;
        max-height: 45vh;
    }

    .grid-productos {
        grid-template-columns: repeat(auto-fill, minmax(140px, 1fr));
    }

    .producto-card .precio {
        font-size: 20px;
    }

    .btn-cobrar {
        font-size: 26px;
        padding: 20px;
    }

    .buscador {
        font-size: 20px;
        padding: 16px;
    }
    }

/* Accesibilidad +40 años */
@media (min-width: 769px) {
    .producto-card {
        padding: 18px;
        min-height: 100px;
    }

    .producto-card .nombre {
        font-size: 16px;
    }

    .producto-card .precio {
        font-size: 22px;
    }

    .btn-cobrar {
        font-size: 26px;
        padding: 22px;
    }
    }
    </style>
    <link rel="manifest" href="/manifest.json">
        <meta name="theme-color" content="#000000">
        <meta name="apple-mobile-web-app-capable" content="yes">
        <meta name="apple-mobile-web-app-title" content="POS Ferretero">
        <script>
            if ('serviceWorker' in navigator) {
                navigator.serviceWorker.register('/sw.js');
                }
        </script>
</head>
<body>

<div class="layout">

    <!-- Panel productos -->
    <div class="panel-productos">
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
                <div class="nombre">{{ $producto->nombre }}</div>
                <div class="precio">$ {{ number_format($producto->precio_venta, 0, ',', '.') }}</div>
                <div class="stock {{ $producto->stock > $producto->stock_minimo ? 'stock-ok' : ($producto->stock > 0 ? 'stock-bajo' : 'stock-cero') }}">
                    {{ $producto->stock > 0 ? 'Stock: ' . $producto->stock : 'Sin stock' }}
                </div>
            </div>
            @endforeach
        </div>
    </div>

    <!-- Panel carrito -->
    <div class="panel-carrito">
        <div class="carrito-header">Venta actual</div>

        <div class="carrito-items" id="carrito-items">
            <div class="empty-carrito" id="empty-msg">
                Toca un producto para agregarlo
            </div>
        </div>

        <div class="carrito-footer">
            <select class="select-cliente" id="select-cliente">
                <option val