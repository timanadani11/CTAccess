<?php

namespace App\Http\Controllers\System;

use App\Http\Controllers\Controller;
use App\Models\Acceso;
use App\Models\Incidencia;
use App\Models\Persona;
use App\Models\UsuarioSistema;
use App\Http\Resources\PersonaResource;
use Carbon\Carbon;
use Inertia\Inertia;
use Illuminate\Http\Request;

class AdminDashboardController extends Controller
{
    public function __construct()
    {
        // Asegurar guard del sistema
        $this->middleware('auth:system');
    }

    public function index()
    {
        $today = Carbon::today();

        // KPI cards
        $stats = [
            'personas' => Persona::count(),
            'usuarios' => UsuarioSistema::count(),
            'accesos_hoy' => Acceso::whereDate('fecha_entrada', $today)->count(),
            'incidencias_7d' => Incidencia::where('created_at', '>=', Carbon::now()->subDays(7))->count(),
        ];

        // Últimos registros
        $recentAccesos = Acceso::with(['persona', 'usuarioEntrada', 'usuarioSalida'])
            ->orderByDesc('fecha_entrada')
            ->limit(10)
            ->get()
            ->map(function ($a) {
                return [
                    'id' => $a->id,
                    'persona' => $a->persona?->Nombre,
                    'fecha_entrada' => $a->fecha_entrada,
                    'fecha_salida' => $a->fecha_salida,
                    'estado' => $a->estado,
                    'entrada_por' => $a->usuarioEntrada?->nombre,
                    'salida_por' => $a->usuarioSalida?->nombre,
                ];
            });

        $recentIncidencias = Incidencia::with(['acceso', 'acceso.persona'])
            ->orderByDesc('created_at')
            ->limit(10)
            ->get()
            ->map(function ($i) {
                return [
                    'id' => $i->incidenciaId ?? $i->id,
                    'tipo' => $i->tipo,
                    'descripcion' => $i->descripcion,
                    'persona' => optional($i->acceso?->persona)->Nombre,
                    'fecha' => $i->created_at,
                ];
            });

        return Inertia::render('System/Admin/Dashboard', [
            'stats' => $stats,
            'recent' => [
                'accesos' => $recentAccesos,
                'incidencias' => $recentIncidencias,
            ],
            'meta' => [
                'generated_at' => now()->toDateTimeString(),
            ],
        ]);
    }

    public function personasView()
    {
        return Inertia::render('System/Admin/Personas', [
            'title' => 'Gestión de Personas'
        ]);
    }

    public function personas(Request $request)
    {
        $perPage = (int) $request->get('per_page', 15);
        $search = $request->get('search', '');

        $query = Persona::query()
            ->orderByDesc('idPersona')
            ->with(['portatiles', 'vehiculos']);

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('Nombre', 'like', "%{$search}%")
                  ->orWhere('documento', 'like', "%{$search}%")
                  ->orWhere('TipoPersona', 'like', "%{$search}%");
            });
        }

        $personas = $query->paginate($perPage)->withQueryString();

        return response()->json([
            'personas' => PersonaResource::collection($personas),
            'filters' => [
                'search' => $search,
                'per_page' => $perPage,
            ],
        ]);
    }
}
