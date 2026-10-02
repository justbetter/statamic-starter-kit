{{--
Heading field.

Usage:
<x-fieldset.heading :heading="$heading?->value()" />

Custom styling:
<x-fieldset.heading :heading="$heading?->value()" class="text-2xl" />

Do's & Don'ts:
- Set the font-size using width, height or size classes.
- Only set a color when the icon should differ from the surrounding text.
- Don't modify the component's core styling. Statamic configuration takes priority.

Statamic configuration:
- preserve_icon_color: When enabled, the original colors from the uploaded SVG are preserved.
--}}

@props(['heading' => false])

@if($heading && $heading['heading'])
    <x-rapidez::tag :is="$heading['heading_type']" :attributes="$attributes->except(['heading'])->twMerge('block font-heading')">
        {!! $heading['heading'] !!}
    </x-rapidez::tag>
@endif
