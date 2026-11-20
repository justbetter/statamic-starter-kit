{{--
A button from the button_variant fieldset: variant, text and icon, without a link.
Use it for a button that does something on the page instead of navigating, such as a form submit or a popover trigger.
For a button with a link use fieldset.button.

Example:
```
<x-fieldset.button-variant :button="$submit_button?->value()" type="submit" />
```

```
<x-fieldset.button-variant :button="$button?->value()" popovertarget="quick-actions" />
```
--}}
@props(['button' => false])

@if ($button && ($button['button_text'] ?? false))
    @php
        $variant = match($button['button_variant']?->value()) {
            default => 'rapidez::button.primary',
            'primary' => 'rapidez::button.primary',
            'secondary' => 'rapidez::button.secondary',
            'outline' => 'rapidez::button.outline',
        };
    @endphp

    <x-dynamic-component :component="$variant" :$attributes>
        <x-fieldset.icon
            :icon="$button->button_options['icon']"
            @class(['h-6', $button->button_options['icon_position']?->value() === 'right' ? 'order-last' : null])
        />

        {{ $button['button_text'] }}
    </x-dynamic-component>
@endif
