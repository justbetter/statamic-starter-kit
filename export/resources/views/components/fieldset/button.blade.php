{{--
This is a single button.
If you need more buttons use the (plural) fieldset.buttons.
For a button without a link, such as a form submit, use fieldset.button-variant.

Example:
```
<x-fieldset.button :button="$button?->value()" />
```

```
<x-fieldset.button :button="$button?->value()" class="mt-4"/>
```
--}}
@props(['button' => false, 'link' => true])

@if ($button && ($button['button_text'] ?? false) && ($button->link['link'] ?? false))
    <x-fieldset.link :link="$button['link']" :disable="!$link" :attributes="$attributes->twMerge('inline-block')">
        <x-fieldset.button-variant :$button class="size-full" tag="span" />
    </x-fieldset.link>
@endif
