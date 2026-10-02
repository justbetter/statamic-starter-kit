@props(['tag' => 'strong'])

<x-rapidez::tag :is="$tag" {{ $attributes->twMerge('block font-heading font-semibold prose-em:not-italic prose-em:text-secondary text-pretty') }}>
    {{ $slot }}
</x-rapidez::tag>