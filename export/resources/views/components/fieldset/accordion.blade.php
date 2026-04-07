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