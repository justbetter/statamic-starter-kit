@if($content->value() ?? false || $media->value() ?? false)
    <div @class([
            'component text-image',
            'has-background' => $background->value(),
        ])
    >
        <div class="container">
            <div class="grid gap-x-14 lg:grid-cols-2 lg:items-center lg:justify-center max-lg:gap-y-10">
                <x-fieldset.content :content="$content?->value()" @class(['lg:order-last' => $toggle->value()])/>
                <x-fieldset.media :media="$media?->value()" class="h-140" />
            </div>
        </div>
    </div>
@endif
