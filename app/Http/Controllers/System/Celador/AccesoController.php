<?php

namespace App\Http\Controllers\System\Celador;

use App\Http\Controllers\Controller;
use App\Models\Acceso;
use Illuminate\Http\Request;
use Inertia\Inertia;

class AccesoController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:system');
    }

    public function index(Request $request)
    {
        $query = Acceso::with(['persona', 'portatil', 'vehiculo'])
            ->latest('fecha_entrada');

        // Filtro de búsqueda por persona
        if ($search = $request->get('q')) {
            $query->whereHas('persona', function ($q) use ($search) {
                $q->where('Nombre', 'like', "%{$search}%")
                  ->orWhere('correo', 'like', "%{$search}%")
                  ->orWhere('numero_documento', 'like', "%{$search}%");
            });
        }

        // Filtro por estado
        if ($estado = $request->get('estado')) {
            $query->where('estado', $estado);
        }

        $accesos = $query->paginate(15)->withQueryString();

        // Estadísticas
        $estadisticas = [
            'total' => Acceso::count(),
            'activos' => Acceso::where('estado', 'activo')->count(),
            'finalizados' => Acceso::where('estado', 'finalizado')->count(),
            'hoy' => Acceso::whereDate('fecha_entrada', today())->count(),
        ];

        return Inertia::render('System/Celador/Accesos/Index', [
            'filters' => [
                'q' => $request->get('q'),
                'estado' => $request->get('estado'),
            ],
            'accesos' => $accesos,
            'estadisticas' => $estadisticas,
        ]);
    }
}

