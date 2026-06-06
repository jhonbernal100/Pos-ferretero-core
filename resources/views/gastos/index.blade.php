@extends('layouts.pos')

@section('titulo', 'Gastos')

@section('contenido')
<div style="padding:16px;max-width:1200px;margin:0 auto;">
    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:16px;">
        <div>
            <h1 style="font-size:22px;">💸 Gastos</h1>
            <p style="font-size:13px;color:#555;">
                Total mes actual:
                <strong>$ {{ number_format($totalMes, 0, ',', '.') }}</strong>
            </p>
        </div>
        <a href="/gastos/crear"
           style="padding:10px 20px;background:#000;color:#fff;border-radius:8px;text-decoration:none;font-size:14px;">
            + Registrar gasto
        </a>
    </div>

    <div style="background:#fff;border-radius:12px;overflow:hidden;">
        <table style="width:100%;border-collapse:collapse;">
            <thead>
                <tr style="background:#000;color:#fff;">
                    <th style="padding:12px;text-align:left;font-size:13px;">Fecha</th>
                    <th style="padding:12px;text-align:left;font-size:13px;">Descripcion</th>
                    <th style="padding:12px;text-align:left;font-size:13px;">Categoria</th>
                    <th style="padding:12px;text-align:right;font-size:13px;">Monto</th>
                    <th style="padding:12px;text-align:center;font-size:13px;">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse($gastos as $gasto)
                <tr style="border-bottom:1px solid #eee;" id="fila-gasto-{{ $gasto->id }}">
                    <td style="padding:12px;font-size:13px;">{{ $gasto->fecha->format('d/m/Y') }}</td>
                    <td style="padding:12px;font-size:13px;">
                        <div style="font-weight:bold;">{{ $gasto->descripcion }}</div>
                        @if($gasto->notas)
                        <div style="font-size:11px;color:#888;">{{ $gasto->notas }}</div>
                        @endif
                    </td>
                    <td style="padding:12px;font-size:13px;">{{ $gasto->categoria }}</td>
                    <td style="padding:12px;font-size:14px;font-weight:bold;text-align:right;color:#c00;">
                        - $ {{ number_format($gasto->monto, 0, ',', '.') }}
                    </td>
                    <td style="padding:12px;text-align:center;">
                        <button onclick="eliminarGasto({{ $gasto->id }})"
                            style="padding:6px 12px;background:#c00;color:#fff;border:none;border-radius:6px;font-size:12px;cursor:pointer;">
                            Eliminar
                        </button>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" style="padding:24px;text-align:center;color:#999;">
                        No hay gastos registrados.
                        <a href="/gastos/crear" style="color:#000;">Registra el primero</a>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div style="margin-top:16px;">
        {{ $gastos->links() }}
    </div>
</div>

@section('scripts')
<script>
async function eliminarGasto(id) {
    if (!confirm('Eliminar este gasto?')) return;

    const res = await fetch(`/gastos/${id}/eliminar`, {
        method: 'DELETE',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
        },
    });

    const data = await res.json();
    if (data.success) {
        document.getElementById('fila-gasto-' + id).remove();
    } else {
        alert('Error: ' + data.mensaje);
    }
}
</script>
@endsection
@endsection