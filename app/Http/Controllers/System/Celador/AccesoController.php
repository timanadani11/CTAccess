<?php

namespace App\Http\Controllers\System\Celador;

use App\Http\Controllers\Controller;
use App\Models\Acceso;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class AccesoController extends Controller
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

        $query = Acceso::with(['persona', 'portatil', 'vehiculo'])
            ->latest('fecha_entrada');

        if ($search = $request->get('q')) {
            $query->whereHas('persona', function ($q) use ($search) {
                $q->where('Nombre', 'like', "%{$search}%")
                  ->orWhere('correo', 'like', "%{$search}%");
            });
        }

        $accesos = $query->paginate(10)->withQueryString();

        return Inertia::render('System/Celador/Accesos/Index', [
            'filters' => ['q' => $request->get('q')],
            'accesos' => $accesos,
        ]);
    }
}
