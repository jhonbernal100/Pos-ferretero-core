<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\VentaController;
use App\Http\Controllers\InventarioController;
use App\Http\Controllers\FerreteriaController;

Route::get('/', fn() => redirect('/login'));

Route::get('/login',   [AuthController::class, 'showLogin'])->name('login');
Route::post('/login',  [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::middleware('auth')->group(function () {

    Route::prefix('ventas')->group(function () {
        Route::get('/',                [VentaController::class, 'index'])->name('ventas.index');
        Route::get('/crear',           [VentaController::class, 'crear'])->name('ventas.crear');
        Route::post('/',               [VentaController::class, 'store'])->name('ventas.store');
        Route::get('/{venta}/ticket',  [VentaController::class, 'ticket'])->name('ventas.ticket');
        Route::post('/{venta}/anular', [VentaController::class, 'anular'])->name('ventas.anular');
    });

    Route::prefix('inventario')->group(function () {
        Route::get('/',          [InventarioController::class, 'index'])->name('inventario.index');
        Route::get('/capturar',  [InventarioController::class, 'capturar'])->name('inventario.capturar');
        Route::post('/analizar', [InventarioController::class, 'analizar'])->name('inventario.analizar');
        Route::post('/guardar',  [InventarioController::class, 'guardar'])->name('inventario.guardar');
        Route::post('/{producto}/actualizar', [InventarioController::class, 'actualizar'])->name('inventario.actualizar');
    });

    Route::prefix('ferreteria')->group(function () {
        Route::get('/perfil',    [FerreteriaController::class, 'perfil'])->name('ferreteria.perfil');
        Route::post('/perfil',   [FerreteriaController::class, 'actualizarPerfil'])->name('ferreteria.actualizar');
        Route::get('/suscripcion', [FerreteriaController::class, 'suscripcion'])->name('ferreteria.suscripcion');
    });

});