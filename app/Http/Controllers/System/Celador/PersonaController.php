<?php

namespace App\Http\Controllers\System\Celador;

use App\Http\Controllers\Controller;
use App\Models\Persona;
use App\Http\Resources\PersonaResource;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class PersonaController extends Controller
{
    /**
     * Display a listing of personas for celador
     */
    public function index(Request $request): Response
    {
        $perPage = (int) $request->get('per_page', 15);
        $search = $request->get('search', '');
        $tipoPersona = $request->get('tipo_persona', '');
        
        $query = Persona::query()
            ->orderByDesc('idPersona')
            ->with(['portatiles', 'vehiculos']);

        // Filtro de búsqueda
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('Nombre', 'like', "%{$search}%")
                  ->orWhere('documento', 'like', "%{$search}%")
                  ->orWhere('correo', 'like', "%{$search}%");
            });
        }

        // Filtro por tipo de persona
        if ($tipoPersona) {
            $query->where('TipoPersona', $tipoPersona);
        }

        $personas = $query->paginate($perPage)->withQueryString();

        // Obtener tipos de persona únicos para el filtro
        $tiposPersona = Persona::select('TipoPersona')
            ->distinct()
            ->whereNotNull('TipoPersona')
            ->orderBy('TipoPersona')
            ->pluck('TipoPersona');

        return Inertia::render('System/Celador/Personas/Index', [
            'personas' => PersonaResource::collection($personas),
            'tiposPersona' => $tiposPersona,
            'filters' => [
                'search' => $search,
                'tipo_persona' => $tipoPersona,
                'per_page' => $perPage,
            ],
        ]);
    }

    /**
     * Display the specified persona
     */
    public function show(Request $request, Persona $persona)
    {
        $persona->load(['portatiles', 'vehiculos', 'accesos' => function($query) {
            $query->orderByDesc('created_at')->limit(10);
        }]);
        
        // Si es una petición AJAX, devolver JSON
        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'persona' => new PersonaResource($persona)
            ]);
        }
        
        // Si no, devolver la vista Inertia
        return Inertia::render('System/Celador/Personas/Show', [
            'persona' => new PersonaResource($persona),
        ]);
    }

    /**
     * Search personas by QR code or document
     */
    public function search(Request $request)
    {
        $query = $request->get('q', '');
        
        if (empty($query)) {
            return response()->json(['personas' => []]);
        }

        $personas = Persona::query()
            ->where(function ($q) use ($query) {
                $q->where('qrCode', $query)
                  ->orWhere('documento', $query)
                  ->orWhere('Nombre', 'like', "%{$query}%");
            })
            ->with(['portatiles', 'vehiculos'])
            ->limit(10)
            ->get();

        return response()->json([
            'personas' => PersonaResource::collection($personas)
        ]);
    }
}
