@props(['media' => false])

@if($media && isset($media['media']))
    @php
        $object_cover = optionalDeep($media)->media_options['object_fit']->value()->get() === 'cover';

        $classes = $attributes->twMerge(
            $object_cover ? 'object-cover w-full h-full' : 'object-contain w-auto! h-auto!'
        );
    @endphp

    @if ($media['media']->isVideo())
        <video {{ $classes }}
            @foreach($media->media_options['video'] as $option)
                {{ $option['value'] }}
            @endforeach
        >
            <source src="{{ $media }}" type="video/mp4">
        </video>
    @else
        @responsive($media['media'], ['class' => $classes->get('class')])
    @endif
@endif