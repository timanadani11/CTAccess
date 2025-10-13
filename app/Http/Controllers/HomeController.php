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
        $hoy = Carbon::today();
        $inicioSemana = Carbon::now()->startOfWeek();
        $inicioMes = Carbon::now()->startOfMonth();
        
        $accesosHoy = Acceso::whereDate('fecha_entrada', $hoy);
        
        $estadisticas = [
            'total_personas' => Persona::count(),
            'accesos_hoy' => $accesosHoy->count(),
            'entradas_hoy' => $accesosHoy->whereNotNull('fecha_entrada')->count(),
            'salidas_hoy' => $accesosHoy->whereNotNull('fecha_salida')->count(),
            'usuarios_dentro' => Acceso::whereNull('fecha_salida')->count(),
            'accesos_semana' => Acceso::where('fecha_entrada', '>=', $inicioSemana)->count(),
            'accesos_mes' => Acceso::where('fecha_entrada', '>=', $inicioMes)->count(),
            'tiempo_promedio' => $this->calcularTiempoPromedio(),
            'incidencias_mes' => 0, // Puedes agregar un modelo de Incidencias si existe
            
            // Legacy (mantener compatibilidad)
            'personas_registradas' => Persona::count(),
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
    
    private function calcularTiempoPromedio()
    {
        $accesos = Acceso::whereNotNull('fecha_salida')
            ->whereDate('fecha_entrada', Carbon::today())
            ->get();
            
        if ($accesos->isEmpty()) {
            return '0h';
        }
        
        $tiempoTotal = 0;
        foreach ($accesos as $acceso) {
            $entrada = Carbon::parse($acceso->fecha_entrada);
            $salida = Carbon::parse($acceso->fecha_salida);
            $tiempoTotal += $entrada->diffInMinutes($salida);
        }
        
        $promedioMinutos = $tiempoTotal / $accesos->count();
        $horas = floor($promedioMinutos / 60);
        $minutos = round($promedioMinutos % 60);
        
        if ($horas > 0) {
            return $horas . 'h ' . $minutos . 'm';
        }
        return $minutos . 'm';
    }
}
