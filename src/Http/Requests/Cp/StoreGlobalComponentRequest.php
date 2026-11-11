<?php

namespace JustBetter\StatamicStarterKit\Http\Requests\CP;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

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
        $setHandle = (string) config('statamic-starter-kit.convert_to_global_component.set_handle', 'global_component');

        return [
            'title' => ['required', 'string', 'max:255'],
            'component' => ['required', 'array'],
            'component.type' => ['required', 'string', Rule::notIn([$setHandle])],
        ];
    }
}
