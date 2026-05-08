<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>POS Ferretero — Avanzas Digital</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: Arial, sans-serif;
            background: #f0f0f0;
            min-height: 100vh;
            display: flex;
        }

        /* Panel izquierdo — Login */
        .panel-login {
            width: 420px;
            min-width: 420px;
            background: #fff;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 40px 36px;
            box-shadow: 4px 0 24px rgba(0,0,0,0.08);
            z-index: 1;
        }

        /* Logo SVG */
        .logo-container {
            text-align: center;
            margin-bottom: 8px;
        }

        .logo-avanzas {
            font-size: 32px;
            font-weight: 900;
            letter-spacing: -1px;
            color: #000;
        }

        .logo-avanzas .av { color: #99CF8E; }
        .logo-avanzas .digital {
            font-size: 14px;
            font-weight: 400;
            color: #CEC8BF;
            display: block;
            letter-spacing: 3px;
            text-transform: uppercase;
            margin-top: -4px;
        }

        .pos-titulo {
            font-size: 22px;
            font-weight: bold;
            color: #000;
            margin: 16px 0 4px;
            text-align: center;
        }

        .version {
            font-size: 11px;
            color: #aaa;
            text-align: center;
            margin-bottom: 8px;
        }

        .slogan {
            font-size: 12px;
            color: #99CF8E;
            text-align: center;
            margin-bottom: 28px;
            font-style: italic;
        }

        /* Separador */
        .divider {
            width: 100%;
            height: 1px;
            background: #f0f0f0;
            margin-bottom: 24px;
        }

        /* Formulario */
        .form-titulo {
            font-size: 18px;
            font-weight: bold;
            color: #333;
            margin-bottom: 20px;
            text-align: center;
        }

        .campo { margin-bottom: 16px; width: 100%; }
        .campo label {
            display: block;
            font-size: 13px;
            color: #555;
            margin-bottom: 6px;
            font-weight: bold;
        }
        .campo input {
            width: 100%;
            padding: 13px 16px;
            font-size: 15px;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            transition: border-color .2s;
            outline: none;
        }
        .campo input:focus { border-color: #99CF8E; }

        .error {
            background: #fdf0f0;
            color: #c00;
            padding: 10px 14px;
            border-radius: 8px;
            font-size: 13px;
            margin-bottom: 16px;
            border-left: 3px solid #c00;
        }

        .btn-login {
            width: 100%;
            padding: 14px;
            background: #000;
            color: #fff;
            border: none;
            border-radius: 10px;
            font-size: 17px;
            font-weight: bold;
            cursor: pointer;
            transition: background .2s;
            margin-top: 4px;
        }

        .btn-login:hover { background: #222; }

        .footer-login {
            margin-top: 28px;
            text-align: center;
            font-size: 11px;
            color: #bbb;
        }

        .footer-login a {
            color: #99CF8E;
            text-decoration: none;
            font-weight: bold;
        }

        /* Panel derecho — Video y marketing */
        .panel-video {
            flex: 1;
            background: linear-gradient(135deg, #1a1a1a 0%, #2d2d2d 100%);
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 40px;
            position: relative;
            overflow: hidden;
        }

        .panel-video::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(153,207,142,0.08) 0%, transparent 60%);
            pointer-events: none;
        }

        .video-titulo {
            color: #fff;
            font-size: 28px;
            font-weight: bold;
            text-align: center;
            margin-bottom: 8px;
            z-index: 1;
        }

        .video-titulo span { color: #99CF8E; }

        .video-subtitulo {
            color: #CEC8BF;
            font-size: 15px;
            text-align: center;
            margin-bottom: 32px;
            z-index: 1;
        }

        .video-wrapper {
            width: 100%;
            max-width: 680px;
            aspect-ratio: 16/9;
            border-radius: 16px;
            overflow: hidden;
            background: #000;
            border: 2px solid #333;
            z-index: 1;
            position: relative;
        }

        .video-wrapper video {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        /* Placeholder cuando no hay video */
        .video-placeholder {
            width: 100%;
            height: 100%;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            background: #111;
            color: #555;
        }

        .video-placeholder .icono { font-size: 64px; margin-bottom: 16px; }
        .video-placeholder p { font-size: 14px; color: #444; }

        /* Features debajo del video */
        .features {
            display: flex;
            gap: 20px;
            margin-top: 28px;
            z-index: 1;
        }

        .feature {
            text-align: center;
            color: #fff;
        }

        .feature .icono { font-size: 28px; margin-bottom: 6px; }
        .feature .texto { font-size: 12px; color: #CEC8BF; line-height: 1.4; }

        /* Responsive */
        @media (max-width: 768px) {
            body { flex-direction: column; }
            .panel-login { width: 100%; min-width: unset; }
            .panel-video { display: none; }
        }
    </style>
</head>
<body>

    <!-- Panel izquierdo — Login -->
    <div class="panel-login">

        <!-- Logo real -->
        <div class="logo-container">
            <img src="/images/logo-pos-ferretero.png" 
            alt="POS Ferretero" 
            style="width:260px; max-width:100%;">
        </div>

        <div class="pos-titulo">🔧 POS Ferretero</div>
        <div class="version">v1.0.{{ str_pad(0, 3, '0', STR_PAD_LEFT) }} — 2026</div>
        <div class="slogan">Tu éxito es nuestro objetivo</div>

        <div class="divider"></div>

        <div class="form-titulo">Iniciar sesión</div>

        @if($errors->any())
        <div class="error">{{ $errors->first() }}</div>
        @endif

        <form method="POST" action="/login" style="width:100%">
            @csrf
            <div class="campo">
                <label>Correo electrónico</label>
                <input type="email" name="email" value="{{ old('email') }}"
                       placeholder="tu@correo.com" required autofocus>
            </div>
            <div class="campo">
                <label>Contraseña</label>
                <input type="password" name="password"
                       placeholder="••••••••" required>
            </div>
            <button type="submit" class="btn-login">Ingresar</button>
        </form>

        <div class="footer-login">
            ¿Necesitas acceso?
            <a href="https://www.avanzas.digital/index.html" target="_blank">
                Contacta a Avanzas Digital
            </a>
        </div>
    </div>

    <!-- Panel derecho — Video -->
    <div class="panel-video">
        <div class="video-titulo">
            El POS más simple para<br>
            <span>ferreterías de barrio</span>
        </div>
        <div class="video-subtitulo">
            Vende, inventaria y controla tu negocio desde cualquier dispositivo
        </div>

        <div class="video-wrapper">
            {{-- Cuando tengas el video, reemplaza el placeholder con: --}}
            {{-- <video autoplay loop muted playsinline src="/videos/demo-pos.mp4"></video> --}}
            <div class="video-placeholder">
                <div class="icono">🎬</div>
                <p>Video demostrativo próximamente</p>
            </div>
        </div>

        <div class="features">
            <div class="feature">
                <div class="icono">📷</div>
                <div class="texto">Inventario<br>con IA</div>
            </div>
            <div class="feature">
                <div class="icono">🖨</div>
                <div class="texto">Tickets<br>térmicos</div>
            </div>
            <div class="feature">
                <div class="icono">💳</div>
                <div class="texto">Control de<br>créditos</div>
            </div>
            <div class="feature">
                <div class="icono">📊</div>
                <div class="texto">Reportes<br>de ventas</div>
            </div>
            <div class="feature">
                <div class="icono">📱</div>
                <div class="texto">PWA<br>móvil</div>
            </div>
        </div>
    </div>

</body>
</html>