<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\VentaController;

Route::get('/', function () {
    return view('welcome');
});

Route::prefix('ventas')->group(function () {
    Route::get('/',                [VentaController::class, 'index'])->name('ventas.index');
    Route::get('/crear',           [VentaController::class, 'crear'])->name('ventas.crear');
    Route::post('/',               [VentaController::class, 'store'])->name('ventas.store');
    Route::get('/{venta}/ticket',  [VentaController::class, 'ticket'])->name('ventas.ticket');
    Route::post('/{venta}/anular', [VentaController::class, 'anular'])->name('ventas.anular');
});