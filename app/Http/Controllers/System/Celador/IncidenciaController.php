<?php

namespace App\Http\Controllers\System\Celador;

use App\Http\Controllers\Controller;
use App\Models\Incidencia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class IncidenciaController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:system');
    }

    protected function ensureRole(): void
    {
        $user = Auth::guard('system')->user();
        if (! $user || ! method_exists($user, 'isCelador') || ! $user->isCelador()) {
            abort(403, 'No autorizado');
        }
    }

    public function index(Request $request)
    {
        $this->ensureRole();

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
