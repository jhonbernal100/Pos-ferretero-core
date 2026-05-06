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
        body { font-family: Arial, sans-serif; background: #f0f0f0; display: flex; flex-direction: column; min-height: 100vh; }

        .navbar {
            background: #000;
            color: #fff;
            padding: 12px 20px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .navbar-brand { font-size: 18px; font-weight: bold; color: #fff; text-decoration: none; }
        .navbar-user { font-size: 12px; color: #aaa; text-align: right; }
        .navbar-user span { display: block; color: #fff; font-size: 14px; }

        .toolbar {
            background: #111;
            padding: 8px 16px;
            display: flex;
            gap: 8px;
            overflow-x: auto;
            scrollbar-width: none;
        }

        .toolbar::-webkit-scrollbar { display: none; }

        .toolbar a {
            color: #fff;
            text-decoration: none;
            padding: 8px 14px;
            border-radius: 6px;
            font-size: 13px;
            white-space: nowrap;
            background: #222;
            border: 1px solid #333;
            transition: background .2s;
        }

        .toolbar a:hover { background: #333; }
        .toolbar a.activo { background: #fff; color: #000; font-weight: bold; }

        .toolbar-logout {
            margin-left: auto;
            background: #c00 !important;
            border-color: #900 !important;
        }

        .contenido-principal { flex: 1; }

        .footer-avanzas {
            background: #000;
            color: #888;
            text-align: center;
            padding: 10px 16px;
            font-size: 11px;
        }

        .footer-avanzas a { color: #fff; text-decoration: none; font-weight: bold; }

        /* Alerta de suscripción próxima a vencer */
        .alerta-trial {
            background: #fff3cd;
            color: #856404;
            text-align: center;
            padding: 8px 16px;
            font-size: 13px;
        }

        .alerta-trial a { color: #856404; font-weight: bold; }
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
            🔧 POS Ferretero
        </a>
        <div class="navbar-user">
            {{ auth()->user()->name ?? 'Usuario' }}
            <span>{{ auth()->user()->tenant->nombre ?? '' }}</span>
        </div>
    </nav>

    {{-- Toolbar según rol --}}
    <div class="toolbar">
        <a href="/ventas/crear"
           class="{{ request()->is('ventas/crear') ? 'activo' : '' }}">
            🛒 Nueva venta
        </a>

        @if(auth()->user()->rol === 'dueno' || auth()->user()->rol === 'superadmin')
            <a href="/ventas"
               class="{{ request()->is('ventas') ? 'activo' : '' }}">
                📋 Ventas
            </a>
            <a href="/inventario"
               class="{{ request()->is('inventario') ? 'activo' : '' }}">
                📦 Inventario
            </a>
        @endif

        <a href="/inventario/capturar"
           class="{{ request()->is('inventario/capturar') ? 'activo' : '' }}">
            📷 Tomar foto
        </a>

        @if(auth()->user()->rol === 'dueno' || auth()->user()->rol === 'superadmin')
            <a href="/creditos"
               class="{{ request()->is('creditos') ? 'activo' : '' }}">
                💳 Créditos
            </a>
            <a href="/ferreteria/perfil"
               class="{{ request()->is('ferreteria/perfil') ? 'activo' : '' }}">
                🏪 Mi ferretería
            </a>
            <a href="/ferreteria/suscripcion"
               class="{{ request()->is('ferreteria/suscripcion') ? 'activo' : '' }}">
                ⭐ Suscripción
            </a>
        @endif

        <form method="POST" action="/logout" style="margin-left:auto">
            @csrf
            <button type="submit"
                style="background:#c00;color:#fff;border:none;padding:8px 14px;border-radius:6px;font-size:13px;cursor:pointer;white-space:nowrap;">
                🚪 Salir
            </button>
        </form>
    </div>

    {{-- Alerta si el trial vence en menos de 7 días --}}
    @auth
    @if(auth()->user()->tenant && auth()->user()->tenant->subscription_status === 'trial')
        @php $dias = auth()->user()->tenant->diasRestantes(); @endphp
        @if($dias <= 7)
        <div class="alerta-trial">
            ⚠️ Tu período de prueba vence en <strong>{{ $dias }} días</strong>.
            <a href="/ferreteria/suscripcion">Contacta a Avanzas Digital para continuar</a>
        </div>
        @endif
    @endif
    @endauth

    <div class="contenido-principal">
        @yield('contenido')
    </div>

    <footer class="footer-avanzas">
        Sistema POS desarrollado por
        <a href="https://www.avanzas.digital/index.html" target="_blank" rel="noopener">Avanzas Digital</a>
        &nbsp;·&nbsp;
        <a href="https://www.avanzas.digital/index.html" target="_blank" rel="noopener">¿Quieres este sistema? Contáctanos</a>
    </footer>

    @yield('scripts')
</body>
</html>