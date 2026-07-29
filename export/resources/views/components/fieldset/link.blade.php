{{--
The fieldset link should be used for every clickable element. For example this fieldset is used inside the button fieldset.

Examples:
```
<x-fieldset.link :link="$link?->value()">
    This is a link
</x-fieldset.link>
```

A common pattern is making an entire card or list item clickable by wrapping it in an <x-fieldset.link> tag
but then also including a <x-fieldset.button> component inside it. This creates a nested anchor (<a> inside <a>),
which is invalid HTML and causes unpredictable browser behavior.
Use the link prop on <x-fieldset.button> for these cases.
Instead of rendering an <a> tag, the button renders as a <span> element — keeping the markup valid while preserving the expected behavior.
```
    <x-fieldset.link :link="$button?->link">
        <x-fieldset.media :media="$media?->value()" />
        <x-fieldset.button :button="$button?->value()" :link="false" />
    </x-fieldset.link>
```
--}}
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