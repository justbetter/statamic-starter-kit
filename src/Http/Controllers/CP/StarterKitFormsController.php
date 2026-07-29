<?php

namespace JustBetter\StatamicStarterKit\Http\Controllers\CP;

use Statamic\Fields\Blueprint;
use Statamic\Forms\Form;
use Statamic\Http\Controllers\CP\Forms\FormsController as BaseFormsController;

class StarterKitFormsController extends BaseFormsController
{
    protected function editFormBlueprint(mixed $form): Blueprint
    {
        $blueprint = parent::editFormBlueprint($form);
        $blueprintContents = $blueprint->contents();

        $emailContentField = [
            'handle' => 'email_content',
            'field' => [
                'type' => 'textarea',
                'display' => __('Email content'),
                'instructions' => '',
            ],
        ];

        $emailContentAvailableFields = [
            'handle' => 'email_content_available_fields',
            'field' => [
                'type' => 'form_email_available_fields',
                'form' => $form instanceof Form ? $form->handle() : null,
                'display' => __('Email content fields'),
                'instructions' => __('justbetter-starter-kit::messages.copy_field_handle_instructions'),
            ],
        ];

        if (isset($blueprintContents['tabs']['main']['sections']) && is_array($blueprintContents['tabs']['main']['sections'])) {
            $sections = $blueprintContents['tabs']['main']['sections'];

            foreach ($sections as $sectionIndex => $section) {
                if (! is_array($section)) {
                    continue;
                }

                $sectionFields = $section['fields'] ?? [];
                if (! is_array($sectionFields)) {
                    continue;
                }

                foreach ($sectionFields as $fieldIndex => $fieldConfig) {
                    if (! is_array($fieldConfig) || ($fieldConfig['handle'] ?? null) !== 'email') {
                        continue;
                    }

                    $fieldDefinition = $fieldConfig['field'] ?? null;
                    if (! is_array($fieldDefinition)) {
                        continue;
                    }

                    $gridFields = $fieldDefinition['fields'] ?? [];
                    if (! is_array($gridFields)) {
                        continue;
                    }

                    $hasEmailContentField = collect($gridFields)->contains(fn (array $field): bool => ($field['handle'] ?? null) === 'email_content');
                    if (! $hasEmailContentField) {
                        $gridFields[] = $emailContentField;
                    }

                    $hasEmailContentAvailableFields = collect($gridFields)->contains(fn (array $field): bool => ($field['handle'] ?? null) === 'email_content_available_fields');
                    if (! $hasEmailContentAvailableFields) {
                        $gridFields[] = $emailContentAvailableFields;
                    }

                    $fieldDefinition['fields'] = $gridFields;
                    $fieldConfig['field'] = $fieldDefinition;
                    $sectionFields[$fieldIndex] = $fieldConfig;
                    $section['fields'] = $sectionFields;
                    $sections[$sectionIndex] = $section;
                }
            }

            $blueprintContents['tabs']['main']['sections'] = $sections;
        }

        $blueprint->setContents($blueprintContents);

        return $blueprint;
    }
}
