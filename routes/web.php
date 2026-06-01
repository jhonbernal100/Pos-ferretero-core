<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\VentaController;
use App\Http\Controllers\InventarioController;
use App\Http\Controllers\FerreteriaController;
use App\Http\Controllers\ProveedorController;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\TrialController;
use App\Http\Controllers\DevolucionController;

Route::get('/', fn() => redirect('/login'));

Route::get('/login',   [AuthController::class, 'showLogin'])->name('login');
Route::post('/login',  [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Rutas públicas — registro trial
Route::prefix('trial')->group(function () {
    Route::get('/',                 [TrialController::class, 'index'])->name('trial.index');
    Route::post('/procesar-rut',    [TrialController::class, 'procesarRut'])->name('trial.procesarRut');
    Route::post('/confirmar',       [TrialController::class, 'confirmar'])->name('trial.confirmar');
    Route::post('/verificar',       [TrialController::class, 'verificar'])->name('trial.verificar');
    Route::post('/reenviar-codigo', [TrialController::class, 'reenviarCodigo'])->name('trial.reenviarCodigo');
});

Route::middleware('auth')->group(function () {

    Route::prefix('ventas')->group(function () {
        Route::get('/',                     [VentaController::class, 'index'])->name('ventas.index');
        Route::get('/crear',                [VentaController::class, 'crear'])->name('ventas.crear');
        Route::post('/',                    [VentaController::class, 'store'])->name('ventas.store');
        Route::get('/{venta}/ticket',       [VentaController::class, 'ticket'])->name('ventas.ticket');
        Route::post('/{venta}/anular',      [VentaController::class, 'anular'])->name('ventas.anular');
        Route::get('/{venta}/ticket-abono', [VentaController::class, 'ticketAbono'])->name('ventas.ticketAbono');
        Route::get('/{venta}/devolucion',   [DevolucionController::class, 'index'])->name('ventas.devolucion');
        Route::post('/{venta}/devolucion',  [DevolucionController::class, 'procesar'])->name('ventas.procesarDevolucion');
    });

    Route::prefix('inventario')->group(function () {
        Route::get('/',                                [InventarioController::class, 'index'])->name('inventario.index');
        Route::get('/capturar',                        [InventarioController::class, 'capturar'])->name('inventario.capturar');
        Route::post('/analizar',                       [InventarioController::class, 'analizar'])->name('inventario.analizar');
        Route::post('/guardar',                        [InventarioController::class, 'guardar'])->name('inventario.guardar');
        Route::get('/crear-manual',                    [InventarioController::class, 'crear'])->name('inventario.crear');
        Route::get('/{producto}/editar',               [InventarioController::class, 'editar'])->name('inventario.editar');
        Route::post('/{producto}/actualizar',          [InventarioController::class, 'actualizar'])->name('inventario.actualizar');
        Route::post('/{producto}/actualizar-producto', [InventarioController::class, 'actualizar_producto'])->name('inventario.actualizarProducto');
        Route::delete('/{producto}/eliminar',          [InventarioController::class, 'eliminar'])->name('inventario.eliminar');
    });

    Route::prefix('ferreteria')->group(function () {
        Route::get('/perfil',      [FerreteriaController::class, 'perfil'])->name('ferreteria.perfil');
        Route::post('/perfil',     [FerreteriaController::class, 'actualizarPerfil'])->name('ferreteria.actualizar');
        Route::get('/suscripcion', [FerreteriaController::class, 'suscripcion'])->name('ferreteria.suscripcion');
    });

    Route::prefix('proveedores')->group(function () {
        Route::get('/',                       [ProveedorController::class, 'index'])->name('proveedores.index');
        Route::get('/crear',                  [ProveedorController::class, 'crear'])->name('proveedores.crear');
        Route::post('/',                      [ProveedorController::class, 'store'])->name('proveedores.store');
        Route::get('/{proveedor}/editar',     [ProveedorController::class, 'editar'])->name('proveedores.editar');
        Route::post('/{proveedor}/actualizar',[ProveedorController::class, 'actualizar'])->name('proveedores.actualizar');
    });

    Route::prefix('clientes')->group(function () {
        Route::get('/',                         [ClienteController::class, 'index'])->name('clientes.index');
        Route::get('/crear',                    [ClienteController::class, 'crear'])->name('clientes.crear');
        Route::post('/',                        [ClienteController::class, 'store'])->name('clientes.store');
        Route::get('/{cliente}/creditos',       [ClienteController::class, 'creditos'])->name('clientes.creditos');
        Route::post('/{cliente}/creditos',      [ClienteController::class, 'guardarCredito'])->name('clientes.guardarCredito');
        Route::get('/{cliente}/historial',      [ClienteController::class, 'historialCredito'])->name('clientes.historial');
        Route::post('/{cliente}/pagar-credito', [ClienteController::class, 'pagarCredito'])->name('clientes.pagarCredito');
        Route::get('/{cliente}/editar',         [ClienteController::class, 'editar'])->name('clientes.editar');
        Route::post('/{cliente}/actualizar',    [ClienteController::class, 'actualizar'])->name('clientes.actualizar');
    });

});