<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreSavedViewRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:80'],
            'filters' => ['required', 'array'],
            'filters.search' => ['nullable', 'string', 'max:255'],
            'filters.status' => ['nullable', 'string', 'max:40'],
            'filters.category' => ['nullable', 'string', 'max:40'],
        ];
    }
}
