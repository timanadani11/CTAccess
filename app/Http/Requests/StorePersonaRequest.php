<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StorePersonaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'documento' => ['nullable', 'string', 'max:255', 'unique:personas,documento'],
            'nombre' => ['required', 'string', 'max:255'],
            'tipoPersona' => ['required', 'string', 'max:255'],
            'foto' => ['nullable', 'string', 'max:2048'],

            'portatiles' => ['sometimes', 'array'],
            'portatiles.*.qrCode' => ['required_with:portatiles', 'string', 'max:255', 'unique:portatiles,qrCode'],
            'portatiles.*.marca' => ['required_with:portatiles', 'string', 'max:255'],
            'portatiles.*.modelo' => ['required_with:portatiles', 'string', 'max:255'],

            'vehiculos' => ['sometimes', 'array'],
            'vehiculos.*.tipo' => ['required_with:vehiculos', 'string', 'max:255'],
            'vehiculos.*.placa' => ['required_with:vehiculos', 'string', 'max:255', 'unique:vehiculos,placa'],
        ];
    }

    public function attributes(): array
    {
        return [
            'nombre' => 'Nombre',
            'tipoPersona' => 'Tipo de Persona',
            'foto' => 'Foto',
            'portatiles.*.qrCode' => 'QR del portátil',
            'vehiculos.*.placa' => 'Placa del vehículo',
        ];
    }
}
