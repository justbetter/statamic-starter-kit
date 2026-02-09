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
            class="size-full"
            tag="span"
        >   
            @if($icon = $button->button_options['icon'])
                @responsive($icon, [
                    'class' => 'h-7 w-auto ' . ($button->button_options['icon_position']?->value() === 'right' ? 'order-last' : null)
                ])
            @endif

            {{ $button['button_text'] }}
        </x-dynamic-component>
    </x-fieldset.link>
@endif