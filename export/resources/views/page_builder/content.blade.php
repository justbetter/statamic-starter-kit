@if($content->value() ?? false)
    <div class="component content">
        <div class="container">
            <x-fieldset.content :content="$content?->value()" />
        </div>
    </div>
@endif
