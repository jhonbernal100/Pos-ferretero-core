<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('titulo', 'POS Ferretero')</title>
    <link rel="manifest" href="/manifest.json">
    <meta name="theme-color" content="#000000">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-title" content="POS Ferretero">
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: Arial, sans-serif; background: #f0f0f0; }

        .navbar {
            background: #000;
            color: #fff;
            padding: 12px 20px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .navbar-brand {
            font-size: 18px;
            font-weight: bold;
            color: #fff;
            text-decoration: none;
        }

        .navbar-links a {
            color: #fff;
            text-decoration: none;
            margin-left: 20px;
            font-size: 15px;
            padding: 8px 14px;
            border-radius: 6px;
            transition: background .2s;
        }

        .navbar-links a:hover {
            background: rgba(255,255,255,0.15);
        }

        .navbar-links a.activo {
            background: rgba(255,255,255,0.25);
        }
    </style>
    @yield('estilos')
    <script>
        if ('serviceWorker' in navigator) {
            navigator.serviceWorker.register('/sw.js');
        }
    </script>
</head>
<body>
    <nav class="navbar">
        <a href="/ventas/crear" class="navbar-brand">
            POS Ferretero
        </a>
        <div class="navbar-links">
            <a href="/ventas/crear"
               class="{{ request()->is('ventas/crear') ? 'activo' : '' }}">
                Nueva venta
            </a>
            <a href="/ventas"
               class="{{ request()->is('ventas') ? 'activo' : '' }}">
                Historial
            </a>
        </div>
    </nav>

    @yield('contenido')

    @yield('scripts')
</body>
</html>