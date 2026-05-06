@extends('layouts.pos')

@section('titulo', 'Proveedores')

@section('contenido')
<div style="padding:16px;max-width:1200px;margin:0 auto;">
    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:16px;">
        <h1 style="font-size:22px;">🚚 Proveedores</h1>
        <a href="/proveedores/crear"
           style="padding:10px 20px;background:#000;color:#fff;border-radius:8px;text-decoration:none;font-size:14px;">
            + Nuevo proveedor
        </a>
    </div>

    <div style="background:#fff;border-radius:12px;overflow:hidden;">
        <table style="width:100%;border-collapse:collapse;">
            <thead>
                <tr style="background:#000;color:#fff;">
                    <th style="padding:12px;text-align:left;font-size:13px;">Nombre</th>
                    <th style="padding:12px;text-align:left;font-size:13px;">NIT</th>
                    <th style="padding:12px;text-align:left;font-size:13px;">Contacto</th>
                    <th style="padding:12px;text-align:left;font-size:13px;">Teléfono</th>
                    <th style="padding:12px;text-align:left;font-size:13px;">Ciudad</th>
                </tr>
            </thead>
            <tbody>
                @forelse($proveedores as $proveedor)
                <tr style="border-bottom:1px solid #eee;">
                    <td style="padding:12px;font-size:14px;font-weight:bold;">{{ $proveedor->nombre }}</td>
                    <td style="padding:12px;font-size:13px;">{{ $proveedor->nit ?? '—' }}</td>
                    <td style="padding:12px;font-size:13px;">{{ $proveedor->contacto ?? '—' }}</td>
                    <td style="padding:12px;font-size:13px;">{{ $proveedor->telefono ?? '—' }}</td>
                    <td style="padding:12px;font-size:13px;">{{ $proveedor->ciudad ?? '—' }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" style="padding:24px;text-align:center;color:#999;">
                        No hay proveedores. <a href="/proveedores/crear" style="color:#000;">Agrega el primero</a>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection