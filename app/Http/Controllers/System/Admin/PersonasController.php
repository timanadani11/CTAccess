<?php

namespace App\Http\Controllers\System\Admin;

use App\Http\Controllers\Controller;
use App\Models\Persona;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Inertia\Inertia;

class PersonasController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth:system', 'check.system.role:administrador']);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'Nombre' => ['required', 'string', 'max:255'],
            'TipoDocumento' => ['required', 'string', 'max:50'],
            'documento' => ['required', 'string', 'max:50', 'unique:personas,documento'],
            'TipoPersona' => ['required', 'string', 'max:100'],
            'Correo' => ['nullable', 'email', 'max:255'],
            'Telefono' => ['nullable', 'string', 'max:50'],
            'Direccion' => ['nullable', 'string', 'max:500'],
            'Empresa' => ['nullable', 'string', 'max:255'],
            'observaciones' => ['nullable', 'string', 'max:1000'],
        ]);

        $persona = Persona::create($validated);

        return back()->with('success', 'Persona creada correctamente');
    }

    public function update(Request $request, Persona $persona)
    {
        $validated = $request->validate([
            'Nombre' => ['required', 'string', 'max:255'],
            'TipoDocumento' => ['required', 'string', 'max:50'],
            'documento' => ['required', 'string', 'max:50', Rule::unique('personas', 'documento')->ignore($persona->idPersona, 'idPersona')],
            'TipoPersona' => ['required', 'string', 'max:100'],
            'Correo' => ['nullable', 'email', 'max:255'],
            'Telefono' => ['nullable', 'string', 'max:50'],
            'Direccion' => ['nullable', 'string', 'max:500'],
            'Empresa' => ['nullable', 'string', 'max:255'],
            'observaciones' => ['nullable', 'string', 'max:1000'],
        ]);

        $persona->update($validated);

        return back()->with('success', 'Persona actualizada correctamente');
    }

    public function destroy(Persona $persona)
    {
        $persona->delete();
        return back()->with('success', 'Persona eliminada correctamente');
    }
}
