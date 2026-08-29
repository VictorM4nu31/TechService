<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateMaintenanceScheduleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->hasAnyRole(['Admin', 'Agente']);
    }

    /**
     * @return array<string, array<int, string>>
     */
    public function rules(): array
    {
        return [
            'equipment_id' => ['required', 'exists:equipment,id'],
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:2000'],
            'frequency_days' => ['required', 'integer', 'min:1', 'max:3650'],
            'next_run_at' => ['nullable', 'date'],
            'is_active' => ['nullable', 'boolean'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'equipment_id.required' => __('El equipo es obligatorio.'),
            'equipment_id.exists' => __('El equipo seleccionado no existe.'),
            'name.required' => __('El nombre de la programación es obligatorio.'),
            'frequency_days.required' => __('La frecuencia es obligatoria.'),
            'frequency_days.min' => __('La frecuencia debe ser de al menos 1 día.'),
        ];
    }
}
