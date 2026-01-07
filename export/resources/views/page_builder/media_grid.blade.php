@if ($media_items?->value() ?? false)
    <div class="component media-grid">
        <div class="container">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                @foreach ($media_items as $media_item)
                    <x-fieldset.media :media="$media_item->media" class="h-120" />
                @endforeach
            </div>
        </div>
    </div>
@endif
