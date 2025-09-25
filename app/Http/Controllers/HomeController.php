<?php

namespace App\Http\Controllers;

use App\Models\Persona;
use App\Models\Acceso;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Carbon\Carbon;

class HomeController extends Controller
{
    public function index()
    {
        // Estadísticas públicas (sin datos sensibles)
        $estadisticas = [
            'personas_registradas' => Persona::count(),
            'accesos_hoy' => Acceso::whereDate('fecha_entrada', Carbon::today())->count(),
            'accesos_activos' => Acceso::whereNull('fecha_salida')->count(),
            'total_accesos' => Acceso::count(),
        ];

        // Información del sistema
        $sistema_info = [
            'nombre' => 'CTAccess',
            'version' => '2.0',
            'descripcion' => 'Sistema de Control de Acceso Inteligente',
            'ultima_actualizacion' => Carbon::now()->format('Y-m-d'),
        ];

        return Inertia::render('Home', [
            'estadisticas' => $estadisticas,
            'sistema_info' => $sistema_info,
        ]);
    }
}
