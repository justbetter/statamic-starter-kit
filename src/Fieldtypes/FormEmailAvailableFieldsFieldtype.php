<?php

namespace JustBetter\StatamicStarterKit\Fieldtypes;

use Illuminate\Support\Collection;
use Statamic\Facades\Form as FormFacade;
use Statamic\Fields\Blueprint;
use Statamic\Fields\Fieldtype;

class FormEmailAvailableFieldsFieldtype extends Fieldtype
{
    /** @var bool */
    protected $selectable = false;

    /** @var bool */
    protected $localizable = false;

    /** @var bool */
    protected $validatable = false;

    /**
     * @return array{available_fields: array<int, array{handle: string, label: string, token: string}>}
     */
    public function preload(): array
    {
        $formHandle = $this->config('form');
        $fields = is_string($formHandle) ? $this->getAvailableFormFields($formHandle) : collect();

        return [
            'available_fields' => $fields->values()->all(),
        ];
    }

    public function preProcess(mixed $data): mixed
    {
        return $data;
    }

    /**
     * @return array<string, array<string, string>>
     */
    protected function configFieldItems(): array
    {
        return [
            'form' => [
                'display' => __('Form'),
                'type' => 'text',
            ],
        ];
    }

    /**
     * @return Collection<int, array{handle: string, label: string, token: string}>
     */
    private function getAvailableFormFields(string $formHandle): Collection
    {
        $form = FormFacade::find($formHandle);

        /** @var Blueprint $blueprint */
        $blueprint = $form->blueprint();
        /** @var Collection<int, array{field: array{display?: string}, handle: string}> $items */
        $items = $blueprint->fields()->items();

        return $items
            ->map(function (array $data): array {
                $handle = $data['handle'];
                $label = $data['field']['display'] ?? $handle;

                return [
                    'handle' => $handle,
                    'label' => $label,
                    'token' => "{{ {$handle} }}",
                ];
            })
            ->values();
    }
}
