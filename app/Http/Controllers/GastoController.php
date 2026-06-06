<?php

namespace App\Http\Controllers;

use App\Models\Gasto;
use Illuminate\Http\Request;
use Carbon\Carbon;

class GastoController extends Controller
{
    public function index()
    {
        $gastos = Gasto::orderByDesc('fecha')
            ->orderByDesc('created_at')
            ->paginate(20);

        $totalMes = Gasto::whereMonth('fecha', now()->month)
            ->whereYear('fecha', now()->year)
            ->sum('monto');

        return view('gastos.index', compact('gastos', 'totalMes'));
    }

    public function crear()
    {
        return view('gastos.crear');
    }

    public function store(Request $request)
    {
        $request->validate([
            'descripcion' => 'required|string|max:191',
            'categoria'   => 'required|string',
            'monto'       => 'required|integer|min:1',
            'fecha'       => 'required|date',
        ]);

        Gasto::create([
            'tenant_id'   => session('tenant_id'),
            'usuario_id'  => auth()->id(),
            'descripcion' => $request->descripcion,
            'categoria'   => $request->categoria,
            'monto'       => $request->monto,
            'fecha'       => $request->fecha,
            'notas'       => $request->notas,
        ]);

        return response()->json([
            'success' => true,
            'mensaje' => 'Gasto registrado correctamente',
        ]);
    }

    public function eliminar(Gasto $gasto)
    {
        $gasto->delete();

        return response()->json([
            'success' => true,
            'mensaje' => 'Gasto eliminado correctamente',
        ]);
    }
}