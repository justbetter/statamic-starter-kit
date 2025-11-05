@props(['buttons' => []])
@slots(['button'])

@if(is_iterable($buttons) && count($buttons['buttons']))
    <x-button.wrapper {{ $attributes }}>
        @foreach($buttons['buttons'] as $content)
            @if (($content['button_text'] ?? false) && ($content['button_link'] ?? false))
                @php
                    $component = match($content['button_variant']->value()) {
                        default => 'button.primary',
                        'secondary' => 'button.secondary',
                        'outline' => 'button.outline'  
                    };
                @endphp

                <x-dynamic-component
                    :attributes="$button->attributes"
                    :href="$content['button_link']"
                    :$component
                >
                    {{ $content['button_text'] }}
                </x-dynamic-component>
            @endif
        @endforeach
    </x-button.wrapper>
@endif