<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0">
    <title>POS Ferretero — Iniciar sesión</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: Arial, sans-serif; background: #f0f0f0; display: flex; align-items: center; justify-content: center; min-height: 100vh; }
        .card { background: #fff; border-radius: 16px; padding: 40px 32px; width: 100%; max-width: 400px; box-shadow: 0 4px 24px rgba(0,0,0,0.08); }
        .logo { text-align: center; margin-bottom: 28px; }
        .logo h1 { font-size: 26px; font-weight: bold; }
        .logo p { color: #888; font-size: 14px; margin-top: 4px; }
        .campo { margin-bottom: 18px; }
        .campo label { display: block; font-size: 14px; color: #555; margin-bottom: 6px; font-weight: bold; }
        .campo input { width: 100%; padding: 14px; font-size: 16px; border: 2px solid #ddd; border-radius: 8px; transition: border-color .2s; }
        .campo input:focus { border-color: #000; outline: none; }
        .error { background: #f8d7da; color: #721c24; padding: 10px 14px; border-radius: 8px; font-size: 14px; margin-bottom: 16px; }
        .btn-login { width: 100%; padding: 16px; background: #000; color: #fff; border: none; border-radius: 10px; font-size: 20px; font-weight: bold; cursor: pointer; margin-top: 8px; }
        .btn-login:hover { background: #222; }
        .footer { text-align: center; margin-top: 28px; font-size: 11px; color: #aaa; }
        .footer a { color: #000; font-weight: bold; text-decoration: none; }
    </style>
</head>
<body>
    <div class="card">
        <div class="logo">
            <h1>POS Ferretero</h1>
            <p>Inicia sesión para continuar</p>
        </div>

        @if($errors->any())
        <div class="error">{{ $errors->first() }}</div>
        @endif

        <form method="POST" action="/login">
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

        <div class="footer">
            Sistema desarrollado por
            <a href="https://www.avanzas.digital/index.html" target="_blank">Avanzas Digital</a>
        </div>
    </div>
</body>
</html>