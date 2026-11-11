@props(['url'])
@php
    $headerBackgroundColor = $globals->email_branding->header_background_color ?? '#4951D5';
    $logo = $globals->email_branding->logo ?? null;
@endphp
<tr>
<td class="header header-top">
<div style="background-color: {{ $headerBackgroundColor }}; width: 870px; margin: auto; padding: 5px 40px;">
<a href="{{ $url }}">
@if ($logo)
<img src="{{ $logo->absoluteUrl() }}" class="logo" alt="{{ $logo->get('alt') ?: config('app.name') }}">
@else
{!! $slot !!}
@endif
</a>
</div>
</td>
</tr>
