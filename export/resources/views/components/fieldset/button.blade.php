@props(['button' => false])

@if ($button && ($button['button_text'] ?? false) && ($button['button_link'] ?? false))
    @php
        $component = match($button['button_variant']->value()) {
            default => 'button.primary',
            'secondary' => 'button.secondary',
            'outline' => 'button.outline'  
        };
    @endphp

    <x-dynamic-component
        :$attributes
        :href="$button['button_link']"
        :$component
    >
        {{ $button['button_text'] }}
    </x-dynamic-component>
@endif