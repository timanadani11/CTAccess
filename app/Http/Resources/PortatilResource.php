<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PortatilResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->portatil_id,
            'qrCode' => $this->qrCode,
            'marca' => $this->marca,
            'modelo' => $this->modelo,
        ];
    }
}
