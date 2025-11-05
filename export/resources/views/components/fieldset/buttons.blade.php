@props(['buttons' => []])
@slots(['button'])

@if(is_iterable($buttons) && count($buttons['buttons']))
    <x-button.wrapper {{ $attributes }}>
        @foreach($buttons['buttons'] as $item)
            <x-fieldset.button :button="$item['button']" :attributes="$button->attributes" />
        @endforeach
    </x-button.wrapper>
@endif