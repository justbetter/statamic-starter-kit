@props(['link' => false, 'disable' => false])

@if($link && $link['link']?->value())
    <x-rapidez::tag
        :is="!$disable && $link['link']->value() ? 'a' : 'div'"
        :href="!$disable && $link['link']->value() ? $link['link']->value() : null"
        :rel="!$disable && $link['link']->value() && $link['link_options'] ? $link->link_options['rel']->value() : null"
        :target="!$disable && $link['link']->value() && $link['link_options'] && $link->link_options['blank'] ? '_blank' : null"
        :aria-label="!$disable && $link['link']->value() && $link['link_options'] ? $link->link_options['aria_label'] : null"
        {{ $attributes }}
    >
        {{ $slot }}
    </x-rapidez::tag>
@endif
