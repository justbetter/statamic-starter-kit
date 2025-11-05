@props(['title' => false])

@if($title && $title['title_text'])
    @php
        $component = match($title->title_options['variant']?->value()) {
            default => 'title.default'
        };
    @endphp

    <x-dynamic-component
        :$attributes
        :$component
    >
        {{ $title['title_text'] }}
    </x-dynamic-component>
@endif