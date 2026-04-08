<?php

namespace JustBetter\StatamicStarterKit\Http\Controllers\CP;

use Statamic\Fields\Blueprint;
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

        if (isset($blueprintContents['tabs']['email']['fields']['email']['field']['fields']) && is_array($blueprintContents['tabs']['email']['fields']['email']['field']['fields'])) {
            $fields = $blueprintContents['tabs']['email']['fields']['email']['field']['fields'];
            $exists = collect($fields)->contains(fn (array $field): bool => ($field['handle'] ?? null) === 'email_content');
            if (! $exists) {
                $fields[] = $emailContentField;
                $blueprintContents['tabs']['email']['fields']['email']['field']['fields'] = $fields;
            }
        }

        if (isset($blueprintContents['tabs']['main']['sections']) && is_array($blueprintContents['tabs']['main']['sections'])) {
            $sections = $blueprintContents['tabs']['main']['sections'];

            foreach ($sections as $sectionIndex => $section) {
                if (! is_array($section) || ($section['display'] ?? null) !== 'Email') {
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

                    $exists = collect($gridFields)->contains(fn (array $field): bool => ($field['handle'] ?? null) === 'email_content');
                    if (! $exists) {
                        $gridFields[] = $emailContentField;

                        $fieldDefinition['fields'] = $gridFields;
                        $fieldConfig['field'] = $fieldDefinition;
                        $sectionFields[$fieldIndex] = $fieldConfig;
                        $section['fields'] = $sectionFields;
                        $sections[$sectionIndex] = $section;
                    }
                }
            }

            $blueprintContents['tabs']['main']['sections'] = $sections;
        }

        $blueprint->setContents($blueprintContents);

        return $blueprint;
    }
}
