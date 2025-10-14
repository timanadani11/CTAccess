<?php

namespace App\Http\Controllers\System\Celador;

use App\Http\Controllers\Controller;
use App\Models\Incidencia;
use Illuminate\Http\Request;
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
}

