<?php

namespace App\Http\Controllers\System;

use App\Http\Controllers\Controller;
use App\Models\Acceso;
use App\Models\Incidencia;
use App\Models\Persona;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class CeladorDashboardController extends Controller
{
    public function __construct()
    {
        // Asegurar guard del sistema
        $this->middleware('auth:system');
    }

    public function index()
    {
        $today = Carbon::today();
        $thisWeek = Carbon::now()->startOfWeek();
        $thisMonth = Carbon::now()->startOfMonth();

        // Estadísticas generales
        $stats = [
            'accesos_hoy' => Acceso::whereDate('fecha_entrada', $today)->count(),
            'accesos_activos' => Acceso::where('estado', 'activo')->count(),
            'incidencias_hoy' => Incidencia::whereDate('created_at', $today)->count(),
            'total_personas' => Persona::count(),
        ];

        // Accesos por día de la semana (últimos 7 días)
        $accesosPorDia = Acceso::select(
                DB::raw('DATE(fecha_entrada) as fecha'),
                DB::raw('COUNT(*) as total')
            )
            ->where('fecha_entrada', '>=', Carbon::now()->subDays(6)->startOfDay())
            ->groupBy('fecha')
            ->orderBy('fecha')
            ->get()
            ->map(function ($item) {
                return [
                    'fecha' => Carbon::parse($item->fecha)->format('d/m'),
                    'dia' => Carbon::parse($item->fecha)->locale('es')->isoFormat('ddd'),
                    'total' => $item->total,
                ];
            });

        // Incidencias por tipo (este mes)
        $incidenciasPorTipo = Incidencia::select('tipo', DB::raw('COUNT(*) as total'))
            ->where('created_at', '>=', $thisMonth)
            ->groupBy('tipo')
            ->get()
            ->map(function ($item) {
                $nombres = [
                    'seguridad' => 'Seguridad',
                    'acceso' => 'Acceso',
                    'equipamiento' => 'Equipamiento',
                    'comportamiento' => 'Comportamiento',
                    'otro' => 'Otro',
                ];
                return [
                    'tipo' => $nombres[$item->tipo] ?? ucfirst($item->tipo),
                    'total' => $item->total,
                ];
            });

        // Incidencias por prioridad (este mes)
        $incidenciasPorPrioridad = Incidencia::select('prioridad', DB::raw('COUNT(*) as total'))
            ->where('created_at', '>=', $thisMonth)
            ->groupBy('prioridad')
            ->get()
            ->map(function ($item) {
                return [
                    'prioridad' => ucfirst($item->prioridad),
                    'total' => $item->total,
                ];
            });

        // Accesos de esta semana vs semana pasada
        $accesosEstaSemana = Acceso::whereBetween('fecha_entrada', [
            $thisWeek,
            Carbon::now()->endOfWeek()
        ])->count();

        $accesosSemanaAnterior = Acceso::whereBetween('fecha_entrada', [
            Carbon::now()->subWeek()->startOfWeek(),
            Carbon::now()->subWeek()->endOfWeek()
        ])->count();

        $stats['accesos_semana'] = $accesosEstaSemana;
        $stats['cambio_semanal'] = $accesosSemanaAnterior > 0 
            ? round((($accesosEstaSemana - $accesosSemanaAnterior) / $accesosSemanaAnterior) * 100, 1)
            : 0;

        return Inertia::render('System/Celador/Dashboard', [
            'stats' => $stats,
            'accesosPorDia' => $accesosPorDia,
            'incidenciasPorTipo' => $incidenciasPorTipo,
            'incidenciasPorPrioridad' => $incidenciasPorPrioridad,
        ]);
    }
}
