{{--
If you want collapsible fields in your component you can use this fieldset.

Examples:
```
<x-fieldset.accordion :accordion="$accordion?->value()" />
```

If you want to have the first accordion open and allow only one to be open at the same time.
```
<x-fieldset.accordion :accordion="$accordion?->value()" open name="single"/>
```

Customize the label and content
```
<x-fieldset.accordion :accordion="$accordion?->value()">
    <x-slot:label class="text-red-700"></x-slot:label>
    <x-slot:content class="text-green-700"></x-slot:content>
</x-fieldset.accordion>
```
--}}
@props(['accordion' => []])
@slots(['label', 'content'])

@if(!empty($accordion['accordion']))
    @foreach($accordion['accordion'] as $accordion)
        <x-rapidez::accordion :attributes="$attributes->twMerge('border-b')">
            <x-slot:label :attributes="$label->attributes->twMerge()">
                <x-prose class="flex-1 min-w-0">
                    {{ $accordion['title'] }}
                </x-prose>
            </x-slot:label>
            <x-slot:content :attributes="$content->attributes->twMerge()">
                <x-fieldset.content :content="$accordion['content']" />
            </x-slot:content>
        </x-rapidez::accordion>
    @endforeach
@endif