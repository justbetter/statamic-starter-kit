@props(['link' => false])

@if($link && $link['link']?->value())  
    <x-rapidez::tag
        :is="$link['link']->value() ? 'a' : 'div'"
        :href="$link['link']->value()"
        :rel="$link['link']->value() && $link['link_options'] ? $link->link_options['rel']->value() : null"
        :target="$link['link']->value() && $link['link_options'] && $link->link_options['blank'] ? '_blank' : null"
        :aria-label="$link['link']->value() && $link['link_options'] ? $link->link_options['aria_label'] : null"
        {{ $attributes }}
    >
        {{ $slot }}
    </x-rapidez::tag>
@endif
