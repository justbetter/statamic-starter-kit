@props(['title' => false])

@if($title && isset($title['title_text']))
    <x-dynamic-component
        :$attributes
        :component="[
            'default' => 'title.default'
        ][$title['variant']] ?? 'title.default'"
    >
        {{ $title['title_text'] }}
    </x-dynamic-component>
@endif