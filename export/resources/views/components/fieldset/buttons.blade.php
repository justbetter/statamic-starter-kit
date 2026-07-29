{{--
Multiple buttons.
If you need one button use the (singular) fieldset.button.

Example:
```
<x-fieldset.buttons :buttons="$buttons?->value()" />
```

If you need to change or add classes for the single buttons use the
`<x-slot:button></x-slot:button>`
```
<x-fieldset.buttons :buttons="$buttons?->value()">
    <x-slot:button class="inline-flex"></x-slot:button>
</x-fieldset.buttons>
```
--}}
@props(['buttons' => []])
@slots(['button'])

@if(is_iterable($buttons) && count($buttons['buttons']))
    <x-button.wrapper {{ $attributes }}>
        @foreach($buttons['buttons'] as $item)
            <x-fieldset.button :button="$item['button']" :attributes="$button->attributes" />
        @endforeach
    </x-button.wrapper>
@endif