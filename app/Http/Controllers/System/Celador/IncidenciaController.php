<?php

namespace App\Http\Controllers\System\Celador;

use App\Http\Controllers\Controller;
use App\Models\Incidencia;
use App\Models\Acceso;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class IncidenciaController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:system');
    }

    public function index(Request $request)
    {
        $query = Incidencia::with([
            'acceso.persona', 
            'reportadoPor:idUsuario,nombre,rol_principal_id',
            'reportadoPor.principalRole:id,nombre'
        ])->latest('created_at');

        // Filtro de búsqueda
        if ($search = $request->get('q')) {
            $query->where(function($q) use ($search) {
                $q->where('descripcion', 'like', "%{$search}%")
                  ->orWhereHas('acceso.persona', function($q) use ($search) {
                      $q->where('Nombre', 'like', "%{$search}%")
                        ->orWhere('Documento', 'like', "%{$search}%");
                  });
            });
        }

        // Filtro por tipo
        if ($tipo = $request->get('tipo')) {
            $query->where('tipo', $tipo);
        }

        // Filtro por prioridad
        if ($prioridad = $request->get('prioridad')) {
            $query->where('prioridad', $prioridad);
        }

        $incidencias = $query->paginate(15)->withQueryString();

        // Estadísticas
        $estadisticas = [
            'total' => Incidencia::count(),
            'alta' => Incidencia::where('prioridad', 'alta')->count(),
            'mes' => Incidencia::whereMonth('created_at', now()->month)
                               ->whereYear('created_at', now()->year)
                               ->count(),
            'hoy' => Incidencia::whereDate('created_at', today())->count(),
        ];

        return Inertia::render('System/Celador/Incidencias/Index', [
            'filters' => [
                'q' => $request->get('q'),
                'tipo' => $request->get('tipo'),
                'prioridad' => $request->get('prioridad'),
            ],
            'incidencias' => $incidencias,
            'estadisticas' => $estadisticas,
        ]);
    }

    /**
     * Get accesos activos para asociar a incidencia
     */
    public function getAccesosActivos()
    {
        $accesos = Acceso::with(['persona:idPersona,Nombre,documento', 'portatil:portatil_id,serial', 'vehiculo:vehiculo_id,placa'])
            ->where('estado', 'activo')
            ->latest('fecha_entrada')
            ->get()
            ->map(function ($acceso) {
                return [
                    'id' => $acceso->id,
                    'persona_nombre' => $acceso->persona->Nombre ?? 'Sin nombre',
                    'persona_documento' => $acceso->persona->documento ?? '',
                    'fecha_entrada' => $acceso->fecha_entrada,
                    'portatil_serial' => $acceso->portatil->serial ?? null,
                    'vehiculo_placa' => $acceso->vehiculo->placa ?? null,
                ];
            });

        return response()->json([
            'accesos' => $accesos
        ]);
    }

    /**
     * Store a newly created incidencia
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'acceso_id' => ['required', 'exists:accesos,id'],
            'tipo' => ['required', 'in:seguridad,acceso,equipamiento,comportamiento,otro'],
            'prioridad' => ['required', 'in:alta,media,baja'],
            'descripcion' => ['required', 'string', 'max:1000'],
        ]);

        /** @var \App\Models\UsuarioSistema $user */
        $user = Auth::guard('system')->user();

        Incidencia::create([
            'acceso_id' => $validated['acceso_id'],
            'tipo' => $validated['tipo'],
            'prioridad' => $validated['prioridad'],
            'descripcion' => $validated['descripcion'],
            'reportado_por' => $user->idUsuario,
        ]);

        return back()->with('success', 'Incidencia reportada exitosamente.');
    }
}


