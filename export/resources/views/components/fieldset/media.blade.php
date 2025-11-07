@props(['media' => false])

@if($media && $media['media'])
    @php
        $objectCover = optionalDeep($media)->media_options['object_fit']->value()->get() === 'cover';

        $classes = $attributes->twMerge(
            $objectCover ? 'object-cover w-full h-full' : 'object-contain w-auto! h-auto!'
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