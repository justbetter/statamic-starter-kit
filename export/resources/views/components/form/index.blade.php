@props([
    'form' => null,
    'formHandle' => null,
    'title' => '',
    'buttonText' => __('Send'),
    'submissionText' => '',
    'formRedirectUrl' => null,
    'id' => '',
    'bottomText' => '',
])
@if($formHandle)
    <div
        class="component form container"
        @if($id) id="{{ $id }}" @endif
        x-data="formSubmit"
        x-load
    >
        <s:form:create
            :in="$formHandle"
            js="alpine:dynamic_form"
            csrf="true"
            x-ref="form"
            id="form-{{ $formHandle }}"
            x-on:submit.prevent="getResults($el, '{{ $formRedirectUrl ? $formRedirectUrl : '' }}')"
            data-invalid-response="{{ __('Invalid server response.') }}"
            data-generic-error="{{ __('Something went wrong. Please try again later.') }}"
        >
            <div class="bg py-18 px-24 max-lg:py-12 max-lg:px-14 max-sm:px-8">
                <x-fieldset.title :$title />
                @if ($submissionText)
                    <div x-cloak x-show="showSuccess">
                        {!! $submissionText !!}
                    </div>
                @endif
                <div class="text-white p-2 mb-6" x-show="formErrors && Object.keys(formErrors).length > 0">
                    <template x-for="error in formErrors" :key="error">
                        <span class="text-red-500" x-text="error"></span><br />
                    </template>
                </div>
                <div x-show="!showSuccess" class="grid max-lg:grid-cols-1 xl:grid-cols-2 xl:gap-14 gap-6">
                    @foreach($sections as $section)
                        <div class="grid grid-cols-1 sm:grid-cols-12 gap-x-8 gap-y-5">
                            @foreach($section['fields'] as $field)
                                @if ($field['visibility'] == 'hidden')
                                    <input id="{{ $field['handle'] }}" name="{{ $field['handle'] }}" type="hidden" class="hidden" value="{{ $field['default'] ?? '' }}">
                                    @continue
                                @endif
                                <template x-if='{!! $show_field[$field['handle']] ?? true !!}'>
                                    <x-rapidez::tag
                                        :is="$field['type'] === 'checkboxes' || $field['type'] === 'radio' ? 'div' : 'label'"
                                        @class([
                                            'col-span-1 sm:col-span-12'=> $field['width'] == 100,
                                            'col-span-1 sm:col-span-9' => $field['width'] == 75,
                                            'col-span-1 sm:col-span-8' => $field['width'] == 66,
                                            'col-span-1 sm:col-span-6' => $field['width'] == 50,
                                            'col-span-1 sm:col-span-4' => $field['width'] == 33,
                                            'col-span-1 sm:col-span-3' => $field['width'] == 25,
                                        ])
                                    >
                                        <x-rapidez::label
                                            class="text-lg font-normal"
                                            x-bind:class="fieldHasError('{{ $field['handle'] }}') ? 'text-red-500' : ''"
                                        >
                                            {{ $field['display'] }}
                                        </x-rapidez::label>
                                        <div>
                                            @includeIf('components.form-fields.'.$field['type'], ['data' => $field])
                                        </div>
                                    </x-rapidez::tag>
                                </template>
                            @endforeach
                        </div>
                    @endforeach
                </div>
                <div x-show="!showSuccess" class="md:flex md:flex-wrap md:items-center md:justify-between mt-11 max-sm:mt-5">
                    <div class="flex flex-wrap">
                        <x-button.primary type="submit" class="w-full!">
                            {{ $buttonText }}
                        </x-button.primary>
                    </div>
                </div>
            </div>

            @if($honeypot ?? false)
                <input name="{!! $honeypot !!}" class="hidden" value="">
            @endif
        </s:form:create>
    </div>
@endif
