@props(['link' => false, 'disable' => false])

@if($link)
    <x-rapidez::tag
        :is="!$disable && $link['link']->url() ? 'a' : 'div'"
        :href="!$disable && $link['link']->url() ? $link['link']->url() : null"
        :rel="!$disable && $link['link']->url() && $link['link_options'] && $link->link_options['rel'] ? collect($link->link_options['rel'])->pluck('value')->implode(' ') : null"
        :target="!$disable && $link['link']->url() && $link['link_options'] && $link->link_options['blank'] ? '_blank' : null"
        :aria-label="!$disable && $link['link']->url() && $link['link_options'] ? $link->link_options['aria_label'] : null" 
        :download="!$disable && $link['link']->url() && $link['link_options'] && $link->link_options['download_attribute'] ? '' : false"
        {{ $attributes }}
    >
        {{ $slot }}
    </x-rapidez::tag>
@endif