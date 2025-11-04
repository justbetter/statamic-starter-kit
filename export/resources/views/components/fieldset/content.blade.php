@props(['content' => []])
@slots(['prose', 'buttons'])

@if(is_iterable($content) && count($content['content'] ?? []))
    @foreach($content['content'] as $data)
        @if($data['type'] === 'text')
            <x-prose :attributes="$prose->attributes->twMerge('fieldset-content data-text')">
                {!! $data['text'] !!}
            </x-prose>
        @elseif($data['type'] === 'buttons')
            <x-fieldset.buttons :buttons="$data->buttons" :attributes="$attributes->twMerge('fieldset-content data-buttons')" />
        @endif
    @endforeach
@endif