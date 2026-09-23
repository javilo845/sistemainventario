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
});

require __DIR__.'/auth.php';
