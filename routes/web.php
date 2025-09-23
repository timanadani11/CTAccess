<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\System\Auth\LoginController as SystemLoginController;
use App\Http\Controllers\System\DashboardController as SystemDashboardController;
use App\Http\Controllers\System\AdminDashboardController;
use App\Http\Controllers\System\CeladorDashboardController;
use App\Http\Controllers\System\Celador\AccesoController as CeladorAccesoController;
use App\Http\Controllers\System\Celador\IncidenciaController as CeladorIncidenciaController;
use App\Http\Controllers\System\Celador\HistorialController as CeladorHistorialController;
use App\Http\Controllers\System\Celador\QrController as CeladorQrController;
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

        // Sección Admin (solo rol administrador)
        Route::middleware('check.system.role:administrador')->group(function () {
            Route::get('/admin/dashboard', [AdminDashboardController::class, 'index'])->name('admin.dashboard');

            // Gestión de usuarios del sistema
            Route::prefix('admin')->name('admin.')->group(function () {
                Route::resource('users', App\Http\Controllers\System\Admin\UsersController::class);
            });
        });

        // Sección Celador (solo rol celador)
        Route::middleware('check.system.role:celador')->prefix('celador')->name('celador.')->group(function () {
            Route::get('/dashboard', [CeladorDashboardController::class, 'index'])->name('dashboard');

            // Accesos
            Route::get('/accesos', [CeladorAccesoController::class, 'index'])->name('accesos.index');

            // Verificación QR
            Route::get('/qr', [CeladorQrController::class, 'index'])->name('qr');

            // Incidencias
            Route::get('/incidencias', [CeladorIncidenciaController::class, 'index'])->name('incidencias.index');

            // Historial
            Route::get('/historial', [CeladorHistorialController::class, 'index'])->name('historial.index');
        });
    });
});


