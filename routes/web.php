<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\System\Auth\LoginController as SystemLoginController;
use App\Http\Controllers\System\DashboardController as SystemDashboardController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Models\Persona;

Route::get('/', function () {
    return Inertia::render('Home');
});

Route::get('/debug/personas/{persona}', function (Persona $persona) {
    $persona->load(['portatiles', 'vehiculos']);
    return view('debug.persona', compact('persona'));
});

// Rutas de Personas optimizadas para Inertia.js
Route::resource('personas', App\Http\Controllers\PersonaController::class);

Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';

// Rutas del sistema (usuarios del sistema: celador, admin)
Route::prefix('system')->name('system.')->group(function () {
    Route::middleware('guest:system')->group(function () {
        Route::get('/login', [SystemLoginController::class, 'create'])->name('login');
        Route::post('/login', [SystemLoginController::class, 'store'])->middleware('throttle:6,1')->name('login.store');
    });

    Route::middleware('auth:system')->group(function () {
        Route::post('/logout', [SystemLoginController::class, 'destroy'])->name('logout');
        Route::get('/panel', [SystemDashboardController::class, 'index'])->name('panel');
    });
});

