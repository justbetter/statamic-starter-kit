<?php

namespace App\Http\Requests\Cp;

use Illuminate\Foundation\Http\FormRequest;

class StoreGlobalComponentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'component' => ['required', 'array'],
            'component.type' => ['required', 'string', 'not_in:global_component'],
        ];
    }
}
