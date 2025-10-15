<?php

namespace App\Http\Controllers\System\Admin;

use App\Http\Controllers\Controller;
use App\Mail\PortatilAsignadoMailable;
use App\Models\Portatil;
use App\Models\Persona;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
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

        // Generar código QR automáticamente para el portátil
        $qrPath = $this->generateQrForPortatil($validated['serial']);
        $validated['qrCode'] = $qrPath;

        $portatil = Portatil::create($validated);

        // Enviar email a la persona con el QR del portátil
        $persona = Persona::find($validated['persona_id']);
        if ($persona && $persona->correo) {
            try {
                Mail::to($persona->correo)->send(new PortatilAsignadoMailable($portatil));
            } catch (\Exception $e) {
                // Log del error pero no fallar la creación
                Log::warning('No se pudo enviar el email de asignación de portátil: ' . $e->getMessage());
            }
        }

        return back()->with('success', 'Portátil registrado exitosamente y QR enviado por email.');
    }

    /**
     * Genera un código QR para un portátil y lo almacena en storage/public/qrcodes
     */
    protected function generateQrForPortatil(string $serial): string
    {
        // Formato: PORTATIL_serial
        $qrContent = 'PORTATIL_' . $serial;
        
        // Construir URL del servicio externo de generación de QR
        $qrUrl = 'https://api.qrserver.com/v1/create-qr-code/?size=300x300&data=' . urlencode($qrContent);
        
        try {
            $response = Http::timeout(10)->get($qrUrl);
            
            if (!$response->ok()) {
                throw new \RuntimeException('No se pudo generar la imagen QR');
            }
            
            // Nombre del archivo
            $filename = sprintf('portatil_%s_%s.png', Str::slug($serial), Str::random(8));
            $path = 'qrcodes/' . $filename;
            
            // Guardar en storage/public/qrcodes
            Storage::disk('public')->put($path, $response->body());
            
            // Retornar la URL pública /storage/qrcodes/...
            return Storage::url($path);
            
        } catch (\Exception $e) {
            // Si falla, retornar null o string vacío
            Log::error('Error generando QR para portátil: ' . $e->getMessage());
            return '';
        }
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
