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
            'idPersona' => $this->idPersona,
            'documento' => $this->documento,
            'Nombre' => $this->Nombre,
            'TipoPersona' => $this->TipoPersona,
            'correo' => $this->correo,
            'qrCode' => $this->qrCode,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'portatiles' => $this->whenLoaded('portatiles'),
            'vehiculos' => $this->whenLoaded('vehiculos'),
            'accesos' => $this->whenLoaded('accesos'),
        ];
    }
}
