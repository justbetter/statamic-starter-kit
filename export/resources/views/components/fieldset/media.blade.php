@props(['media' => false])

@if($media && $media['media'])
    @php
        $objectCover = optionalDeep($media)->media_options['object_fit']->value()->get() === 'cover';

        $classes = $attributes->twMerge(
            $objectCover ? '**:object-cover **:size-full' : 'w-auto! h-auto!'
        );
    @endphp

    <div {{ $classes }}>
        @if ($media['media']->isVideo())
            <video
                @foreach($media->media_options['video'] as $option)
                    {{ $option['value'] }}
                @endforeach
            >
                <source src="{{ $media['media'] }}" type="video/mp4">
            </video>
        @else
            @responsive($media['media'])
        @endif
    </div>
@endif
