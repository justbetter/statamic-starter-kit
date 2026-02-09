@props(['tag' => 'div'])

<x-rapidez::tag :is="$tag" {{ $attributes->twMerge('text-2xl lg:text-4xl font-semibold') }}>
    {{ $slot }}
</x-rapidez::tag>