@php
    $faqItems = \Statamic\Statamic::tag('faq:getItems')->params(['type' => $faq_type?->value()?->value(), 'items' => $faq_items?->get(), 'categories' => $faq_categories?->get()])->fetch();
@endphp

@if($faqItems?->isNotEmpty() ?? false)
    <div class="component faq">
        <div class="container">
            <x-fieldset.title :title="$title?->value()" class="mb-5" />
            
            @foreach($faqItems as $faq)
                <x-rapidez::accordion class="border-b" name="single" open>
                    <x-slot:label class="text-lg">
                        {{ $faq->title }}
                    </x-slot:label>
                    <x-slot:content>
                        <x-fieldset.content :content="$faq->answer_content" />
                    </x-slot:content>
                </x-rapidez::accordion>
            @endforeach
        </div>
    </div>
@endif