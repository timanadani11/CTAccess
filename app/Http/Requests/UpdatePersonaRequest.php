<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdatePersonaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $personaParam = $this->route('persona');
        // When using Route Model Binding, $personaParam may be a model instance
        $personaId = is_object($personaParam) ? ($personaParam->idPersona ?? null) : $personaParam;

        return [
            'documento' => [
                'nullable', 'string', 'max:255',
                Rule::unique('personas', 'documento')->ignore($personaId, 'idPersona'),
            ],
            'nombre' => ['sometimes', 'required', 'string', 'max:255'],
            'tipoPersona' => ['sometimes', 'required', 'string', 'max:255'],
            'foto' => ['nullable', 'string', 'max:2048'],

            'portatiles' => ['sometimes', 'array'],
            'portatiles.*.id' => ['sometimes', 'integer', 'exists:portatiles,portatil_id'],
            'portatiles.*.qrCode' => ['required_with:portatiles', 'string', 'max:255'],
            'portatiles.*.marca' => ['required_with:portatiles', 'string', 'max:255'],
            'portatiles.*.modelo' => ['required_with:portatiles', 'string', 'max:255'],

            'vehiculos' => ['sometimes', 'array'],
            'vehiculos.*.id' => ['sometimes', 'integer', 'exists:vehiculos,id'],
            'vehiculos.*.tipo' => ['required_with:vehiculos', 'string', 'max:255'],
            'vehiculos.*.placa' => ['required_with:vehiculos', 'string', 'max:255'],
        ];
    }
}
