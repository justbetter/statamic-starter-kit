{{--
Everything that has to do with writing content in a bard.

Example:
```
<x-fieldset.content :content="$content?->value()" />
```

In all fieldsets we don't recommend using the slots to add custom styling/spacing,
change this globally inside the main component.

But if you really need it you can do it like this.
```
<x-fieldset.content :content="$content?->value()">
    <x-slot:buttons class="mt-4"></x-slot:buttons>
    <x-slot:button class="mt-4"></x-slot:button>
    <x-slot:prose class="mt-4"></x-slot:prose>
</x-fieldset.content>
```

If you want to add an image you can do it like this:
```
    ...
    @elseif($data['type'] === 'image')
        <x-fieldset.media :media="$media?->value()" :attributes="$media->attributes->twMerge('fieldset-content data-media')"/>
    @endif
```
And don't forget to add `media` to your @slots
--}}
@props(['content' => []])
@slots(['prose', 'buttons', 'button'])

@if(is_iterable($content) && count($content['content']))
    <div {{ $attributes }}>
        @foreach($content['content'] as $data)
            @if($data['type'] === 'text')
                <x-prose :attributes="$prose->attributes->twMerge('fieldset-content data-text')">
                    {!! $data['text'] !!}
                </x-prose>
            @elseif($data['type'] === 'buttons')
                <x-fieldset.buttons :buttons="$data->buttons" :attributes="$buttons->attributes->twMerge('fieldset-content data-buttons')">
                    <x-slot:button :attributes="$button->attributes"></x-slot:button>
                </x-fieldset.buttons>
            @endif
        @endforeach
    </div>
@endif
