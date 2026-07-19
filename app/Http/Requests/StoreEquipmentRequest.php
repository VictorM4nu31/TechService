<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreEquipmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, array<int, string>>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'brand' => ['nullable', 'string', 'max:100'],
            'model' => ['nullable', 'string', 'max:100'],
            'serial_number' => ['nullable', 'string', 'max:150'],
            'type' => ['required', 'string', 'in:Computadora,Impresora,Red,Servidor,Teléfono,Otro'],
            'client_id' => ['nullable', 'exists:clients,id'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'name.required' => __('El nombre del equipo es obligatorio.'),
            'type.required' => __('El tipo de equipo es obligatorio.'),
            'type.in' => __('El tipo de equipo debe ser uno de los valores permitidos.'),
            'client_id.exists' => __('El cliente seleccionado no existe.'),
        ];
    }
}
