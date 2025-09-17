<?php

namespace App\Services;

use App\Models\Persona;
use App\Models\Portatil;
use App\Models\Portatiles;
use App\Models\Vehiculo;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class PersonaService
{
    /**
     * Descargar imagen PNG de un QR externo y almacenarla en storage/public/qrcodes.
     * Retorna la URL pública /storage/qrcodes/....png
     */
    protected function storeQrPng(string $content, string $prefix = 'qr'): string
    {
        // Construir URL del servicio externo
        $qrUrl = 'https://api.qrserver.com/v1/create-qr-code/?size=300x300&data=' . urlencode($content);
        $response = Http::get($qrUrl);
        if (!$response->ok()) {
            throw new \RuntimeException('No se pudo generar la imagen QR');
        }
        $filename = sprintf('%s_%s_%s.png', $prefix, Str::slug($content), Str::random(8));
        $path = 'qrcodes/' . $filename; // dentro del disco public
        Storage::disk('public')->put($path, $response->body());
        return Storage::url($path); // /storage/qrcodes/...
    }

    public function createWithRelations(array $data): Persona
    {
        return DB::transaction(function () use ($data) {
            // Generar QR para la persona a partir del documento (si viene)
            $personaQrPath = null;
            if (!empty($data['documento'])) {
                $personaQrPath = $this->storeQrPng($data['documento'], 'persona');
            }

            $persona = Persona::create([
                'documento' => $data['documento'] ?? null,
                'Nombre' => $data['nombre'],
                'TipoPersona' => $data['tipoPersona'],
                // Guardamos la RUTA de la imagen QR en el campo qrCode
                'qrCode' => $personaQrPath,
                'correo' => $data['correo'] ?? null,
            ]);

            if (!empty($data['portatiles']) && is_array($data['portatiles'])) {
                foreach ($data['portatiles'] as $p) {
                    // Generar QR para el portátil usando su serial
                    $serial = $p['serial'] ?? '';
                    $qrPath = $serial ? $this->storeQrPng($serial, 'portatil') : null;
                    $persona->portatiles()->create([
                        // Guardamos la RUTA de la imagen QR del portátil
                        'qrCode' => $qrPath,
                        'serial' => $serial,
                        'marca' => $p['marca'],
                        'modelo' => $p['modelo'],
                    ]);
                }
            }

            if (!empty($data['vehiculos']) && is_array($data['vehiculos'])) {
                foreach ($data['vehiculos'] as $v) {
                    $persona->vehiculos()->create([
                        'tipo' => $v['tipo'],
                        'placa' => $v['placa'],
                    ]);
                }
            }

            return $persona->load(['portatiles', 'vehiculos']);
        });
    }

    public function updateWithRelations(Persona $persona, array $data): Persona
    {
        return DB::transaction(function () use ($persona, $data) {
            $persona->update([
                'documento' => $data['documento'] ?? $persona->documento,
                'Nombre' => $data['nombre'] ?? $persona->Nombre,
                'TipoPersona' => $data['tipoPersona'] ?? $persona->TipoPersona,
                // Si viene documento, regeneramos QR; en caso contrario, conservamos el existente
                'qrCode' => isset($data['documento']) && $data['documento'] !== null
                    ? $this->storeQrPng($data['documento'], 'persona')
                    : $persona->qrCode,
                'correo' => $data['correo'] ?? $persona->correo,
            ]);

            if (array_key_exists('portatiles', $data) && is_array($data['portatiles'])) {
                foreach ($data['portatiles'] as $p) {
                    if (!empty($p['id'])) {
                        $portatil = Portatiles::where('portatil_id', $p['id'])->where('persona_id', $persona->idPersona)->firstOrFail();
                        $portatil->update([
                            'qrCode' => isset($p['serial']) && $p['serial'] !== null
                                ? $this->storeQrPng($p['serial'], 'portatil')
                                : $portatil->qrCode,
                            'serial' => $p['serial'] ?? $portatil->serial,
                            'marca' => $p['marca'] ?? $portatil->marca,
                            'modelo' => $p['modelo'] ?? $portatil->modelo,
                        ]);
                    } else {
                        $qrPath = !empty($p['serial']) ? $this->storeQrPng($p['serial'], 'portatil') : null;
                        $persona->portatiles()->create([
                            'qrCode' => $qrPath,
                            'serial' => $p['serial'] ?? null,
                            'marca' => $p['marca'],
                            'modelo' => $p['modelo'],
                        ]);
                    }
                }
            }

            if (array_key_exists('vehiculos', $data) && is_array($data['vehiculos'])) {
                foreach ($data['vehiculos'] as $v) {
                    if (!empty($v['id'])) {
                        $vehiculo = Vehiculo::where('id', $v['id'])->where('persona_id', $persona->idPersona)->firstOrFail();
                        $vehiculo->update([
                            'tipo' => $v['tipo'] ?? $vehiculo->tipo,
                            'placa' => $v['placa'] ?? $vehiculo->placa,
                        ]);
                    } else {
                        $persona->vehiculos()->create([
                            'tipo' => $v['tipo'],
                            'placa' => $v['placa'],
                        ]);
                    }
                }
            }

            return $persona->load(['portatiles', 'vehiculos']);
        });
    }
}

