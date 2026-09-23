<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

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
});

require __DIR__.'/auth.php';
