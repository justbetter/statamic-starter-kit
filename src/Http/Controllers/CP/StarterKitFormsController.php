<?php

namespace JustBetter\StatamicStarterKit\Http\Controllers\CP;

use Statamic\Contracts\Forms\Form;
use Statamic\Fields\Blueprint;
use Statamic\Http\Controllers\CP\Forms\FormsController as BaseFormsController;

class StarterKitFormsController extends BaseFormsController
{
    /**
     * @param  Form  $form
     * @return Blueprint
     */
    protected function editFormBlueprint($form)
    {
        $blueprint = parent::editFormBlueprint($form);
        $blueprintContents = $blueprint->contents();

        /** @var array<string, array{handle: string, field: array<string, array<string, mixed>>}> $emailFields */
        $emailFields = $blueprintContents['tabs']['email']['fields']?->toArray() ?? [];

        $emailFields['email']['field']['fields'][] = [
            'handle' => 'email_content',
            'field' => [
                'type' => 'textarea',
                'display' => __('Email content'),
                'instructions' => '',
            ],
        ];

        $blueprintContents['tabs']['email']['fields'] = collect($emailFields);
        $blueprint->setContents($blueprintContents);

        return $blueprint;
    }
}
