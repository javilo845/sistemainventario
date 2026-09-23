<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', [\App\Http\Controllers\DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Módulo Organizacional (Fase 2)
    Route::resource('empresas', \App\Http\Controllers\EmpresaController::class);
    Route::resource('sucursales', \App\Http\Controllers\SucursalController::class);
    Route::resource('areas', \App\Http\Controllers\AreaController::class);

    // Módulo de Catálogo Maestro (Fase 3)
    Route::get('items/{item}/inventario', [\App\Http\Controllers\ItemController::class, 'inventario'])->name('items.inventario');
    Route::resource('items', \App\Http\Controllers\ItemController::class);
    Route::resource('categorias', \App\Http\Controllers\CategoriaController::class);
    Route::resource('proveedores', \App\Http\Controllers\ProveedorController::class);

    // Módulo de Movimientos y Núcleo de Inventario (Fase 4)
    Route::get('movimientos', [\App\Http\Controllers\MovimientoInventarioController::class, 'index'])->name('movimientos.index');
    Route::get('movimientos/crear', [\App\Http\Controllers\MovimientoInventarioController::class, 'create'])->name('movimientos.create');
    Route::post('movimientos/entrada', [\App\Http\Controllers\MovimientoInventarioController::class, 'storeEntrada'])->name('movimientos.entrada');
    Route::post('movimientos/salida', [\App\Http\Controllers\MovimientoInventarioController::class, 'storeSalida'])->name('movimientos.salida');
    Route::post('movimientos/traslado', [\App\Http\Controllers\MovimientoInventarioController::class, 'storeTraslado'])->name('movimientos.traslado');
    Route::post('movimientos/ajuste', [\App\Http\Controllers\MovimientoInventarioController::class, 'storeAjuste'])->name('movimientos.ajuste');
    Route::get('movimientos/consultar-stock', [\App\Http\Controllers\MovimientoInventarioController::class, 'consultarStock'])->name('movimientos.consultar-stock');

    // Módulo de Reportes y Exportación (Fase 5)
    Route::get('reportes/inventario', [\App\Http\Controllers\ReporteController::class, 'inventario'])->name('reportes.inventario');
    Route::get('reportes/inventario/exportar-excel', [\App\Http\Controllers\ReporteController::class, 'exportarInventarioExcel'])->name('reportes.inventario.excel');
    Route::get('reportes/inventario/exportar-pdf', [\App\Http\Controllers\ReporteController::class, 'exportarInventarioPdf'])->name('reportes.inventario.pdf');

    Route::get('reportes/movimientos', [\App\Http\Controllers\ReporteController::class, 'movimientos'])->name('reportes.movimientos');
    Route::get('reportes/movimientos/exportar-excel', [\App\Http\Controllers\ReporteController::class, 'exportarMovimientosExcel'])->name('reportes.movimientos.excel');
    Route::get('reportes/movimientos/exportar-pdf', [\App\Http\Controllers\ReporteController::class, 'exportarMovimientosPdf'])->name('reportes.movimientos.pdf');
});

require __DIR__.'/auth.php';
