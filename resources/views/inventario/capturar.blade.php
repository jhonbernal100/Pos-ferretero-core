<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Capturar Producto</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: Arial, sans-serif; background: #f0f0f0; }

        .navbar { background: #000; color: #fff; padding: 14px 16px; display: flex; align-items: center; gap: 12px; }
        .navbar a { color: #fff; text-decoration: none; font-size: 20px; }
        .navbar h1 { font-size: 18px; }

        .container { padding: 16px; max-width: 600px; margin: 0 auto; }

        .camara-box {
            background: #fff;
            border-radius: 12px;
            padding: 20px;
            text-align: center;
            margin-bottom: 16px;
        }

        .btn-camara {
            width: 100%;
            padding: 20px;
            background: #000;
            color: #fff;
            border: none;
            border-radius: 10px;
            font-size: 20px;
            font-weight: bold;
            cursor: pointer;
            margin-bottom: 12px;
        }

        .btn-camara.secundario {
            background: #444;
            font-size: 16px;
            padding: 14px;
        }

        #preview-img {
            width: 100%;
            max-height: 300px;
            object-fit: contain;
            border-radius: 8px;
            display: none;
            margin-bottom: 12px;
        }

        .estado {
            padding: 12px;
            border-radius: 8px;
            font-size: 14px;
            margin-bottom: 12px;
            display: none;
        }

        .estado.procesando { background: #fff3cd; color: #856404; }
        .estado.error      { background: #f8d7da; color: #721c24; }
        .estado.existente  { background: #d4edda; color: #155724; }

        .formulario {
            background: #fff;
            border-radius: 12px;
            padding: 20px;
            display: none;
        }

        .formulario h2 { font-size: 18px; margin-bottom: 16px; }

        .campo { margin-bottom: 14px; }
        .campo label { display: block; font-size: 13px; color: #555; margin-bottom: 4px; }
        .campo input, .campo select {
            width: 100%;
            padding: 12px;
            font-size: 16px;
            border: 2px solid #ddd;
            border-radius: 8px;
        }
        .campo input:focus, .campo select:focus {
            border-color: #000;
            outline: none;
        }

        .fila-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; }

        .alerta-existente {
            background: #fff3cd;
            border: 2px solid #ffc107;
            border-radius: 8px;
            padding: 14px;
            margin-bottom: 16px;
            font-size: 14px;
        }

        .alerta-existente strong { display: block; font-size: 16px; margin-bottom: 6px; }

        .btn-guardar {
            width: 100%;
            padding: 18px;
            background: #000;
            color: #fff;
            border: none;
            border-radius: 10px;
            font-size: 20px;
            font-weight: bold;
            cursor: pointer;
            margin-top: 8px;
        }

        .btn-actualizar {
            width: 100%;
            padding: 14px;
            background: #28a745;
            color: #fff;
            border: none;
            border-radius: 10px;
            font-size: 18px;
            font-weight: bold;
            cursor: pointer;
            margin-top: 8px;
        }

        .footer-brand {
            text-align: center;
            padding: 20px;
            font-size: 12px;
            color: #aaa;
        }

        .footer-brand a { color: #000; }
    </style>
</head>
<body>

<div class="navbar">
    <a href="/ventas/crear">←</a>
    <h1>Capturar producto</h1>
</div>

<div class="container">

    <div class="camara-box">
        <img id="preview-img" src="" alt="Vista previa">

        <label for="input-camara">
            <div class="btn-camara">📷 Tomar foto del producto</div>
        </label>
        <input type="file" id="input-camara" accept="image/*" capture="environment"
               style="display:none" onchange="procesarFoto(this)">

        <label for="input-galeria">
            <div class="btn-camara secundario">🖼 Seleccionar de galería</div>
        </label>
        <input type="file" id="input-galeria" accept="image/*"
               style="display:none" onchange="procesarFoto(this)">
    </div>

    <div class="estado procesando" id="estado-procesando">
        ⏳ Analizando imagen con IA... por favor espera
    </div>

    <div class="estado error" id="estado-error"></div>

    <div class="formulario" id="formulario">
        <h2 id="form-titulo">Nuevo producto</h2>

        <div class="alerta-existente" id="alerta-existente" style="display:none">
            <strong>⚠️ Producto ya existe en inventario</strong>
            <span id="existente-info"></span>
            <br><br>
            <label>Agregar unidades al stock existente:</label>
            <input type="number" id="agregar-stock" min="1" value="1"
                   style="width:100%;padding:10px;font-size:18px;border:2px solid #000;border-radius:8px;margin-top:6px">
            <button class="btn-actualizar" onclick="actualizarStock()">
                ✅ Actualizar stock
            </button>
        </div>

        <div id="form-nuevo">
            <div class="campo">
                <label>Nombre del producto *</label>
                <input type="text" id="f-nombre" placeholder="Ej: Puntilla 2 pulgadas galvanizada">
            </div>

            <div class="fila-2">
                <div class="campo">
                    <label>Marca</label>
                    <input type="text" id="f-marca" placeholder="Ej: Pernos SA">
                </div>
                <div class="campo">
                    <label>Referencia</label>
                    <input type="text" id="f-referencia" placeholder="Ej: PN-2G">
                </div>
            </div>

            <div class="fila-2">
                <div class="campo">
                    <label>Categoría</label>
                    <select id="f-categoria">
                        <option value="Tornillería">Tornillería</option>
                        <option value="Herramientas">Herramientas</option>
                        <option value="Construcción">Construcción</option>
                        <option value="Eléctrico">Eléctrico</option>
                        <option value="Plomería">Plomería</option>
                        <option value="Pintura">Pintura</option>
                        <option value="Seguridad">Seguridad</option>
                        <option value="General">General</option>
                    </select>
                </div>
                <div class="campo">
                    <label>Unidad</label>
                    <select id="f-unidad">
                        <option value="unidad">Unidad</option>
                        <option value="metro">Metro</option>
                        <option value="kilo">Kilo</option>
                        <option value="litro">Litro</option>
                        <option value="bolsa">Bolsa</option>
                        <option value="caja">Caja</option>
                    </select>
                </div>
            </div>

            <div class="campo">
                <label>Proveedor</label>
                <select id="f-proveedor">
                    <option value="">Sin proveedor</option>
                    @foreach($proveedores as $proveedor)
                    <option value="{{ $proveedor->id }}">{{ $proveedor->nombre }}</option>
                    @endforeach
                </select>
            </div>

            <div class="fila-2">
                <div class="campo">
                    <label>Precio costo (COP) *</label>
                    <input type="number" id="f-precio-compra" placeholder="0" min="0">
                </div>
                <div class="campo">
                    <label>Precio venta (COP) *</label>
                    <input type="number" id="f-precio-venta" placeholder="0" min="0">
                </div>
            </div>

            <div class="fila-2">
                <div class="campo">
                    <label>Cantidad *</label>
                    <input type="number" id="f-stock" placeholder="0" min="0">
                </div>
                <div class="campo">
                    <label>Stock mínimo</label>
                    <input type="number" id="f-stock-minimo" placeholder="5" min="0" value="5">
                </div>
            </div>

            <button class="btn-guardar" onclick="guardarProducto()">
                💾 Guardar en inventario
            </button>
        </div>
    </div>

    <div class="footer-brand">
        Sistema POS desarrollado por
        <a href="https://www.avanzas.digital/index.html" target="_blank">Avanzas Digital</a>
    </div>

</div>

<script>
let fotoBase64 = null;   
let productoExistenteId = null;

async function procesarFoto(input) {
    const file = input.files[0];
    if (!file) return;

    // Mostrar preview
    const reader = new FileReader();
    reader.onload = e => {
        document.getElementById('preview-img').src = e.target.result;
        document.getElementById('preview-img').style.display = 'block';
    };
    reader.readAsDataURL(file);

    // Convertir a base64
    const base64 = await fileToBase64(file);
    fotoBase64 = base64; // ← esta línea debe estar

    // Mostrar estado procesando
    mostrarEstado('procesando');
    document.getElementById('formulario').style.display = 'none';

    try {
        const res = await fetch('/inventario/analizar', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ imagen: base64 }),
        });

        const data = await res.json();

        if (!data.success) {
            mostrarError(data.mensaje);
            return;
        }

        // Ocultar estado procesando
        ocultarEstados();

        // Llenar formulario
        const p = data.producto;
        document.getElementById('f-nombre').value     = p.nombre || '';
        document.getElementById('f-marca').value      = p.marca || '';
        document.getElementById('f-referencia').value = p.referencia || '';
        setSelect('f-categoria', p.categoria);
        setSelect('f-unidad', p.unidad);

        // Verificar si existe
        if (data.existente) {
            productoExistenteId = data.existente.id;
            document.getElementById('alerta-existente').style.display = 'block';
            document.getElementById('form-nuevo').style.display = 'none';
            document.getElementById('form-titulo').textContent = '⚠️ Producto existente';
            document.getElementById('existente-info').textContent =
                `${data.existente.nombre} — Stock actual: ${data.existente.stock} unidades`;
        } else {
            productoExistenteId = null;
            document.getElementById('alerta-existente').style.display = 'none';
            document.getElementById('form-nuevo').style.display = 'block';
            document.getElementById('form-titulo').textContent = '✅ Nuevo producto detectado';
        }

        document.getElementById('formulario').style.display = 'block';

    } catch (e) {
        mostrarError('Error de conexión: ' + e.message);
    }
}

function fileToBase64(file) {
    return new Promise((resolve, reject) => {
        const reader = new FileReader();
        reader.onload  = () => resolve(reader.result.split(',')[1]);
        reader.onerror = reject;
        reader.readAsDataURL(file);
    });
}

function setSelect(id, value) {
    const sel = document.getElementById(id);
    for (let opt of sel.options) {
        if (opt.value === value) { sel.value = value; break; }
    }
}

function mostrarEstado(tipo) {
    ocultarEstados();
    document.getElementById('estado-' + tipo).style.display = 'block';
}

function mostrarError(msg) {
    ocultarEstados();
    const el = document.getElementById('estado-error');
    el.textContent = '❌ ' + msg;
    el.style.display = 'block';
}

function ocultarEstados() {
    document.getElementById('estado-procesando').style.display = 'none';
    document.getElementById('estado-error').style.display = 'none';
}

async function guardarProducto() {
    const payload = {
        nombre:        document.getElementById('f-nombre').value,
        marca:         document.getElementById('f-marca').value,
        referencia:    document.getElementById('f-referencia').value,
        categoria:     document.getElementById('f-categoria').value,
        unidad:        document.getElementById('f-unidad').value,
        precio_compra: parseInt(document.getElementById('f-precio-compra').value) || 0,
        precio_venta:  parseInt(document.getElementById('f-precio-venta').value) || 0,
        stock:         parseInt(document.getElementById('f-stock').value) || 0,
        stock_minimo:  parseInt(document.getElementById('f-stock-minimo').value) || 5,
        foto_base64:   fotoBase64,
    };

    if (!payload.nombre) { alert('El nombre del producto es obligatorio'); return; }
    if (!payload.precio_venta) { alert('El precio de venta es obligatorio'); return; }
    if (!payload.stock) { alert('La cantidad es obligatoria'); return; }

    try {
        const res = await fetch('/inventario/guardar', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(payload),
        });

        const data = await res.json();

        if (data.success) {
            alert('✅ ' + data.mensaje);
            window.location.reload();
        } else {
            alert('❌ Error: ' + data.mensaje);
        }
    } catch (e) {
        alert('Error de conexión: ' + e.message);
    }
}

async function actualizarStock() {
    const cantidad = parseInt(document.getElementById('agregar-stock').value) || 0;
    if (!cantidad || cantidad <= 0) { alert('Ingresa una cantidad válida'); return; }

    try {
        const res = await fetch(`/inventario/${productoExistenteId}/actualizar`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ stock: cantidad }),
        });

        const data = await res.json();

        if (data.success) {
            alert('✅ ' + data.mensaje);
            window.location.reload();
        } else {
            alert('❌ Error: ' + data.mensaje);
        }
    } catch (e) {
        alert('Error de conexión: ' + e.message);
    }
}
</script>

</body>
</html>