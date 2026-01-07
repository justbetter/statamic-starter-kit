@if ($logos?->value() ?? false)
    <div class="component logo-grid">
        <div class="container">
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                @foreach ($logos as $logo)
                    @if ($logo->logo ?? false)
                        <div class="py-8 px-4 bg">
                            @responsive($logo->logo, ['class' => 'max-h-full mx-auto w-auto h-12 sm:h-16'])
                        </div>
                    @endif
                @endforeach
            </div>
        </div>
    </div>
@endif
