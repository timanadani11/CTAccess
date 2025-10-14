<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\System\Auth\LoginController as SystemLoginController;
use App\Http\Controllers\System\DashboardController as SystemDashboardController;
use App\Http\Controllers\System\AdminDashboardController;
use App\Http\Controllers\System\CeladorDashboardController;
use App\Http\Controllers\System\Celador\AccesoController as CeladorAccesoController;
use App\Http\Controllers\System\Celador\IncidenciaController as CeladorIncidenciaController;
use App\Http\Controllers\System\Celador\HistorialController as CeladorHistorialController;
use App\Http\Controllers\System\Celador\QrController as CeladorQrController;
use App\Http\Controllers\System\Celador\PersonaController as CeladorPersonaController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Models\Persona;

Route::get('/', [HomeController::class, 'index'])->name('home');

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
        Route::middleware('check.system.role:administrador')->prefix('admin')->name('admin.')->group(function () {
            Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

            // Gestión de usuarios del sistema
            Route::resource('users', App\Http\Controllers\System\Admin\UsersController::class);
            
            // Gestión de permisos
            Route::resource('permissions', App\Http\Controllers\System\Admin\PermissionsController::class)->only(['index', 'store', 'update', 'destroy']);
            
            // Gestión de personas
            Route::get('personas', [AdminDashboardController::class, 'personasView'])->name('personas');
            Route::get('personas/data', [AdminDashboardController::class, 'personas'])->name('personas.data');
            Route::post('personas', [App\Http\Controllers\System\Admin\PersonasController::class, 'store'])->name('personas.store');
            Route::put('personas/{persona}', [App\Http\Controllers\System\Admin\PersonasController::class, 'update'])->name('personas.update');
            Route::delete('personas/{persona}', [App\Http\Controllers\System\Admin\PersonasController::class, 'destroy'])->name('personas.destroy');

            // Gestión de portátiles
            Route::get('portatiles', [App\Http\Controllers\System\Admin\PortatilesController::class, 'index'])->name('portatiles.index');
            Route::get('portatiles/data', [App\Http\Controllers\System\Admin\PortatilesController::class, 'data'])->name('portatiles.data');
            Route::get('portatiles/personas', [App\Http\Controllers\System\Admin\PortatilesController::class, 'personas'])->name('portatiles.personas');
            Route::post('portatiles', [App\Http\Controllers\System\Admin\PortatilesController::class, 'store'])->name('portatiles.store');
            Route::put('portatiles/{portatil}', [App\Http\Controllers\System\Admin\PortatilesController::class, 'update'])->name('portatiles.update');
            Route::delete('portatiles/{portatil}', [App\Http\Controllers\System\Admin\PortatilesController::class, 'destroy'])->name('portatiles.destroy');

            // Gestión de vehículos
            Route::get('vehiculos', [App\Http\Controllers\System\Admin\VehiculosController::class, 'index'])->name('vehiculos.index');
            Route::get('vehiculos/data', [App\Http\Controllers\System\Admin\VehiculosController::class, 'data'])->name('vehiculos.data');
            Route::get('vehiculos/personas', [App\Http\Controllers\System\Admin\VehiculosController::class, 'personas'])->name('vehiculos.personas');
            Route::post('vehiculos', [App\Http\Controllers\System\Admin\VehiculosController::class, 'store'])->name('vehiculos.store');
            Route::put('vehiculos/{vehiculo}', [App\Http\Controllers\System\Admin\VehiculosController::class, 'update'])->name('vehiculos.update');
            Route::delete('vehiculos/{vehiculo}', [App\Http\Controllers\System\Admin\VehiculosController::class, 'destroy'])->name('vehiculos.destroy');

            // Accesos (reutilizando controlador del celador)
            Route::get('/accesos', [CeladorAccesoController::class, 'index'])->name('accesos.index');

            // Verificación QR (reutilizando controlador del celador)
            Route::get('/qr', [CeladorQrController::class, 'index'])->name('qr.index');
            Route::post('/qr/registrar', [CeladorQrController::class, 'registrarAcceso'])->name('qr.registrar');
            Route::get('/qr/accesos-activos', [CeladorQrController::class, 'accesosActivos'])->name('qr.accesos-activos');
            Route::get('/qr/historial', [CeladorQrController::class, 'historialDelDia'])->name('qr.historial');
            Route::get('/qr/estadisticas', [CeladorQrController::class, 'estadisticas'])->name('qr.estadisticas');
            Route::post('/qr/buscar-persona', [CeladorQrController::class, 'buscarPersona'])->name('qr.buscar-persona');
            Route::post('/qr/buscar-cedula', [CeladorQrController::class, 'buscarPersonaPorCedula'])->name('qr.buscar-cedula');

            // Incidencias (reutilizando controlador del celador)
            Route::get('/incidencias', [CeladorIncidenciaController::class, 'index'])->name('incidencias.index');

            // Historial / Reportes (reutilizando controlador del celador)
            Route::get('/historial', [CeladorHistorialController::class, 'index'])->name('historial.index');
            Route::get('/historial/export-pdf', [CeladorHistorialController::class, 'exportPDF'])->name('historial.export-pdf');
        });

        // Sección Celador (solo rol celador)
        Route::middleware('check.system.role:celador')->prefix('celador')->name('celador.')->group(function () {
            Route::get('/dashboard', [CeladorDashboardController::class, 'index'])->name('dashboard');

            // Personas
            Route::get('/personas', [CeladorPersonaController::class, 'index'])->name('personas.index');
            Route::get('/personas/search', [CeladorPersonaController::class, 'search'])->name('personas.search');
            Route::get('/personas/{persona}', [CeladorPersonaController::class, 'show'])->name('personas.show');

            // Accesos
            Route::get('/accesos', [CeladorAccesoController::class, 'index'])->name('accesos.index');

            // Verificación QR
            Route::get('/qr', [CeladorQrController::class, 'index'])->name('qr.index');
            Route::post('/qr/registrar', [CeladorQrController::class, 'registrarAcceso'])->name('qr.registrar');
            Route::get('/qr/accesos-activos', [CeladorQrController::class, 'accesosActivos'])->name('qr.accesos-activos');
            Route::get('/qr/historial', [CeladorQrController::class, 'historialDelDia'])->name('qr.historial');
            Route::get('/qr/estadisticas', [CeladorQrController::class, 'estadisticas'])->name('qr.estadisticas');
            Route::post('/qr/buscar-persona', [CeladorQrController::class, 'buscarPersona'])->name('qr.buscar-persona');
            Route::post('/qr/buscar-cedula', [CeladorQrController::class, 'buscarPersonaPorCedula'])->name('qr.buscar-cedula');

            // Incidencias
            Route::get('/incidencias', [CeladorIncidenciaController::class, 'index'])->name('incidencias.index');

            // Historial / Reportes
            Route::get('/historial', [CeladorHistorialController::class, 'index'])->name('historial.index');
            Route::get('/historial/export-pdf', [CeladorHistorialController::class, 'exportPDF'])->name('historial.export-pdf');
        });
    });
});

