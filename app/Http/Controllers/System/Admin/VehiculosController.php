<?php

namespace App\Http\Controllers\System\Admin;

use App\Http\Controllers\Controller;
use App\Models\Vehiculo;
use App\Models\Persona;
use Illuminate\Http\Request;
use Inertia\Inertia;

class VehiculosController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Inertia::render('System/Admin/Vehiculos/Index');
    }

    /**
     * Get paginated data for vehículos
     */
    public function data(Request $request)
    {
        $query = Vehiculo::with('persona:idPersona,Nombre,documento');

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('placa', 'like', "%{$search}%")
                  ->orWhere('tipo', 'like', "%{$search}%")
                  ->orWhereHas('persona', function($q) use ($search) {
                      $q->where('Nombre', 'like', "%{$search}%")
                        ->orWhere('documento', 'like', "%{$search}%");
                  });
            });
        }

        $vehiculos = $query->orderBy('created_at', 'desc')
                          ->paginate($request->per_page ?? 15);

        return response()->json([
            'vehiculos' => $vehiculos
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
            'tipo' => ['required', 'string', 'max:50'],
            'placa' => ['required', 'string', 'max:20', 'unique:vehiculos,placa'],
        ]);

        Vehiculo::create($validated);

        return back()->with('success', 'Vehículo registrado exitosamente.');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Vehiculo $vehiculo)
    {
        $validated = $request->validate([
            'persona_id' => ['required', 'exists:personas,idPersona'],
            'tipo' => ['required', 'string', 'max:50'],
            'placa' => ['required', 'string', 'max:20', 'unique:vehiculos,placa,' . $vehiculo->id],
        ]);

        $vehiculo->update($validated);

        return back()->with('success', 'Vehículo actualizado exitosamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Vehiculo $vehiculo)
    {
        $vehiculo->delete();

        return back()->with('success', 'Vehículo eliminado exitosamente.');
    }
}
