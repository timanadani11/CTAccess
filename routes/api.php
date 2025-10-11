<?php

use Illuminate\Support\Facades\Route;
use App\Models\Acceso;

/**
 * API pública para accesos recientes (usado en Home con WebSockets)
 */
Route::get('/accesos/recientes', function () {
    try {
        $accesos = Acceso::with('persona')
            ->whereDate('fecha_entrada', today())
            ->latest('fecha_entrada')
            ->limit(10)
            ->get()
            ->map(function ($acceso) {
                return [
                    'id' => $acceso->id,
                    'persona' => $acceso->persona->Nombre,
                    'documento' => $acceso->persona->documento,
                    'tipo' => $acceso->fecha_salida ? 'salida' : 'entrada',
                    'tiempo' => $acceso->fecha_salida 
                        ? $acceso->fecha_salida->toIso8601String()
                        : $acceso->fecha_entrada->toIso8601String()
                ];
            });

        return response()->json($accesos);
    } catch (\Exception $e) {
        \Log::error('Error en /api/accesos/recientes: ' . $e->getMessage());
        return response()->json(['error' => 'Error al obtener accesos'], 500);
    }
});

