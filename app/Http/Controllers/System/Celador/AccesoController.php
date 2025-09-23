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

