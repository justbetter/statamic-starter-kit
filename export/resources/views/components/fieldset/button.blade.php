@props(['button' => false, 'link' => true])

@if ($button && ($button['button_text'] ?? false) && ($button->link['link'] ?? false))
    @php
        $variant = match($button['button_variant']->value()) {
            default => 'button.primary',
            'secondary' => 'button.secondary',
            'outline' => 'button.outline'
        };
    @endphp

    <x-fieldset.link :link="$button['link']" :disable="!$link" :attributes="$attributes->twMerge('inline-block')">
        <x-dynamic-component
            :component="$variant"
            tag="span"
            class="size-full"
        >
            {{ $button['button_text'] }}
        </x-dynamic-component>
    </x-fieldset.link>
@endif