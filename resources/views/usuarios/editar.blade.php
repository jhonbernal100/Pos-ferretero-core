@extends('layouts.pos')

@section('titulo', 'Editar Usuario')

@section('contenido')
<div style="max-width:520px;margin:24px auto;padding:0 16px;">
    <div style="background:#fff;border-radius:12px;padding:24px;">
        <h2 style="font-size:20px;margin-bottom:6px;">Editar usuario</h2>
        <p style="font-size:13px;color:#888;margin-bottom:20px;">
            Deja la contrasena en blanco para no cambiarla.
        </p>

        <div id="mensaje" style="display:none;padding:10px;border-radius:8px;margin-bottom:12px;font-size:14px;"></div>

        <div style="margin-bottom:14px;">
            <label style="display:block;font-size:13px;color:#555;margin-bottom:4px;">Nombre completo *</label>
            <input type="text" id="f-name" value="{{ $usuario->name }}"
                style="width:100%;padding:12px;font-size:16px;border:2px solid #ddd;border-radius:8px;">
        </div>

        <div style="margin-bottom:14px;">
            <label style="display:block;font-size:13px;color:#555;margin-bottom:4px;">Correo electronico *</label>
            <input type="email" id="f-email" value="{{ $usuario->email }}"
                style="width:100%;padding:12px;font-size:16px;border:2px solid #ddd;border-radius:8px;">
        </div>

        <div style="margin-bottom:14px;">
            <label style="display:block;font-size:13px;color:#555;margin-bottom:4px;">Rol *</label>
            <select id="f-rol"
                style="width:100%;padding:12px;font-size:16px;border:2px solid #ddd;border-radius:8px;">
                <option value="auxiliar" {{ $usuario->rol === 'auxiliar' ? 'selected' : '' }}>
                    Auxiliar — puede vender e inventariar
                </option>
                <option value="dueno" {{ $usuario->rol === 'dueno' ? 'selected' : '' }}>
                    Gerente — acceso completo
                </option>
            </select>
        </div>

        <div style="margin-bottom:20px;">
            <label style="display:block;font-size:13px;color:#555;margin-bottom:4px;">
                Nueva contrasena (opcional)
            </label>
            <input type="password" id="f-password" placeholder="Minimo 8 caracteres"
                style="width:100%;padding:12px;font-size:16px;border:2px solid #ddd;border-radius:8px;">
            <div style="font-size:11px;color:#888;margin-top:3px;">
                Deja en blanco para mantener la contrasena actual
            </div>
        </div>

        <button onclick="guardar()"
            style="width:100%;padding:16px;background:#000;color:#fff;border:none;border-radius:10px;font-size:18px;font-weight:bold;cursor:pointer;">
            Guardar cambios
        </button>

        <a href="/usuarios"
            style="display:block;text-align:center;margin-top:12px;color:#555;font-size:14px;">
            Volver a usuarios
        </a>
    </div>
</div>

@section('scripts')
<script>
async function guardar() {
    const name     = document.getElementById('f-name').value;
    const email    = document.getElementById('f-email').value;
    const rol      = document.getElementById('f-rol').value;
    const password = document.getElementById('f-password').value;

    if (!name)  { alert('El nombre es obligatorio'); return; }
    if (!email) { alert('El correo es obligatorio'); return; }

    const payload = { name, email, rol };
    if (password) payload.password = password;

    const res = await fetch('/usuarios/{{ $usuario->id }}/actualizar', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
        },
        body: JSON.stringify(payload),
    });

    const data = await res.json();
    const msg  = document.getElementById('mensaje');
    msg.style.display    = 'block';
    msg.style.background = data.success ? '#d4edda' : '#f8d7da';
    msg.style.color      = data.success ? '#155724' : '#721c24';
    msg.textContent      = data.mensaje;

    if (data.success) {
        setTimeout(() => window.location.href = '/usuarios', 1500);
    }
}
</script>
@endsection
@endsection