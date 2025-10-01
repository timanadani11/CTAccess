<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PersonaResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->idPersona, // Alias para compatibilidad
            'idPersona' => $this->idPersona,
            'documento' => $this->documento,
            'Nombre' => $this->Nombre,
            'TipoPersona' => $this->TipoPersona,
            'correo' => $this->correo,
            'qrCode' => $this->qrCode, // Ya viene como /storage/qrcodes/xxx.png
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'createdAt' => $this->created_at?->format('Y-m-d H:i:s'), // Formato legible
            'updatedAt' => $this->updated_at?->format('Y-m-d H:i:s'), // Formato legible
            // Relaciones
            'portatiles' => $this->whenLoaded('portatiles', function() {
                return $this->portatiles->map(function($portatil) {
                    return [
                        'id' => $portatil->portatil_id,
                        'portatil_id' => $portatil->portatil_id,
                        'serial' => $portatil->serial,
                        'marca' => $portatil->marca,
                        'modelo' => $portatil->modelo,
                        'qrCode' => $portatil->qrCode,
                    ];
                });
            }),
            'vehiculos' => $this->whenLoaded('vehiculos', function() {
                return $this->vehiculos->map(function($vehiculo) {
                    return [
                        'id' => $vehiculo->vehiculo_id,
                        'vehiculo_id' => $vehiculo->vehiculo_id,
                        'tipo' => $vehiculo->tipo,
                        'placa' => $vehiculo->placa,
                        'marca' => $vehiculo->marca ?? null,
                        'modelo' => $vehiculo->modelo ?? null,
                        'color' => $vehiculo->color ?? null,
                    ];
                });
            }),
            'accesos' => $this->whenLoaded('accesos', function() {
                return $this->accesos->map(function($acceso) {
                    return [
                        'id' => $acceso->acceso_id,
                        'acceso_id' => $acceso->acceso_id,
                        'fecha_entrada' => $acceso->fecha_entrada,
                        'fecha_salida' => $acceso->fecha_salida,
                        'estado' => $acceso->estado,
                        'tipo_acceso' => $acceso->tipo_acceso ?? 'Entrada',
                        'created_at' => $acceso->created_at?->format('Y-m-d H:i:s'),
                    ];
                });
            }),
        ];
    }
}
