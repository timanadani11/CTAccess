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
            'id' => $this->idPersona,
            'documento' => $this->documento,
            'nombre' => $this->Nombre,
            'tipoPersona' => $this->TipoPersona,
            'foto' => $this->Foto,
            'createdAt' => $this->created_at,
            'updatedAt' => $this->updated_at,
            'portatiles' => PortatilResource::collection($this->whenLoaded('portatiles')),
            'vehiculos' => VehiculoResource::collection($this->whenLoaded('vehiculos')),
        ];
    }
}
