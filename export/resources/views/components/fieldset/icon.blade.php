@props(['icon' => false])

@if ($icon)
    <span {{ $attributes->twMerge('h-8 inline-flex items-center justify-center *:w-auto *:h-full shrink-0' . (!$icon['preserve_icon_color'] ? ' [&_[fill]:not([fill=none])]:fill-current [&_[stroke]:not([stroke=none])]:stroke-current' : '')) }}>
        {!! $icon['icon'] !!}
    </span>
@endif