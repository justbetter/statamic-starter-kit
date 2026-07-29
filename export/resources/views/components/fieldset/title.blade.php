{{--
Everything that has to do with titles.

Examples:
```
<x-fieldset.title :title="$title?->value()" />
```

We don't recommend adding custom classes on the title fieldset,
change this globally inside the main component.
```
<x-fieldset.title :title="$title?->value()" class="text-primary"/>
```

If you want to have different variants add them like this:
```
$component = match($title->title_options['variant']?->value()) {
    default => 'title.component',
    'component-title' => 'title.component',
    'page-title' => 'page.title'
};
```
--}}
@props(['title' => false])

@if($title && $title['title_text'])
    @php
        $component = match($title->title_options['variant']?->value()) {
            default => 'title.default'
        };
    @endphp

    <x-dynamic-component
        :tag="$title->title_options['heading_type']->value() ?? 'div'"
        :$attributes
        :$component
    >
        {{ $title['title_text'] }}
    </x-dynamic-component>
@endif
