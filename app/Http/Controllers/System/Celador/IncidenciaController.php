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
        $query = Incidencia::with(['acceso.persona'])
            ->latest('created_at');

        if ($search = $request->get('q')) {
            $query->where('descripcion', 'like', "%{$search}%");
        }

        $incidencias = $query->paginate(10)->withQueryString();

        return Inertia::render('System/Celador/Incidencias/Index', [
            'filters' => ['q' => $request->get('q')],
            'incidencias' => $incidencias,
        ]);
    }
}

