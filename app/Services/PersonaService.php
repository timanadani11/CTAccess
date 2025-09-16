<?php

namespace App\Services;

use App\Models\Persona;
use App\Models\Portatil;
use App\Models\Vehiculo;
use Illuminate\Support\Facades\DB;

class PersonaService
{
    public function createWithRelations(array $data): Persona
    {
        return DB::transaction(function () use ($data) {
            $persona = Persona::create([
                'documento' => $data['documento'] ?? null,
                'Nombre' => $data['nombre'],
                'TipoPersona' => $data['tipoPersona'],
                'Foto' => $data['foto'] ?? null,
            ]);

            if (!empty($data['portatiles']) && is_array($data['portatiles'])) {
                foreach ($data['portatiles'] as $p) {
                    $persona->portatiles()->create([
                        'qrCode' => $p['qrCode'],
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
                'Foto' => $data['foto'] ?? $persona->Foto,
            ]);

            if (array_key_exists('portatiles', $data) && is_array($data['portatiles'])) {
                foreach ($data['portatiles'] as $p) {
                    if (!empty($p['id'])) {
                        $portatil = Portatil::where('portatil_id', $p['id'])->where('persona_id', $persona->idPersona)->firstOrFail();
                        $portatil->update([
                            'qrCode' => $p['qrCode'] ?? $portatil->qrCode,
                            'marca' => $p['marca'] ?? $portatil->marca,
                            'modelo' => $p['modelo'] ?? $portatil->modelo,
                        ]);
                    } else {
                        $persona->portatiles()->create([
                            'qrCode' => $p['qrCode'],
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
