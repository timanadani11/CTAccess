<?php

namespace App\Http\Controllers\System\Admin;

use App\Http\Controllers\Controller;
use App\Models\Portatil;
use App\Models\Persona;
use Illuminate\Http\Request;
use Inertia\Inertia;

class PortatilesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Inertia::render('System/Admin/Portatiles/Index');
    }

    /**
     * Get paginated data for portátiles
     */
    public function data(Request $request)
    {
        $query = Portatil::with('persona:idPersona,Nombre,documento');

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('serial', 'like', "%{$search}%")
                  ->orWhere('marca', 'like', "%{$search}%")
                  ->orWhere('modelo', 'like', "%{$search}%")
                  ->orWhere('qrCode', 'like', "%{$search}%")
                  ->orWhereHas('persona', function($q) use ($search) {
                      $q->where('Nombre', 'like', "%{$search}%")
                        ->orWhere('documento', 'like', "%{$search}%");
                  });
            });
        }

        $portatiles = $query->orderBy('created_at', 'desc')
                           ->paginate($request->per_page ?? 15);

        return response()->json([
            'portatiles' => $portatiles
        ]);
    }

    /**
     * Get all personas for select dropdown
     */
    public function personas()
    {
        $personas = Persona::select('idPersona as id', 'Nombre as nombre', 'documento', 'TipoPersona as tipo_persona')
                          ->orderBy('Nombre')
                          ->get();

        return response()->json([
            'personas' => $personas
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'persona_id' => ['required', 'exists:personas,idPersona'],
            'serial' => ['required', 'string', 'max:255', 'unique:portatiles,serial'],
            'qrCode' => ['nullable', 'string', 'max:255'],
            'marca' => ['required', 'string', 'max:100'],
            'modelo' => ['required', 'string', 'max:100'],
        ]);

        Portatil::create($validated);

        return back()->with('success', 'Portátil registrado exitosamente.');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Portatil $portatil)
    {
        $validated = $request->validate([
            'persona_id' => ['required', 'exists:personas,idPersona'],
            'serial' => ['required', 'string', 'max:255', 'unique:portatiles,serial,' . $portatil->portatil_id . ',portatil_id'],
            'qrCode' => ['nullable', 'string', 'max:255'],
            'marca' => ['required', 'string', 'max:100'],
            'modelo' => ['required', 'string', 'max:100'],
        ]);

        $portatil->update($validated);

        return back()->with('success', 'Portátil actualizado exitosamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Portatil $portatil)
    {
        $portatil->delete();

        return back()->with('success', 'Portátil eliminado exitosamente.');
    }
}
