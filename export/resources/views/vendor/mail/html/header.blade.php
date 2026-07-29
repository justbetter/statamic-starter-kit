@props(['url'])
@php
    $headerBackgroundColor = $globals->email_branding->header_background_color ?? '#4951D5';
@endphp
<tr>
<td class="header header-top">
<div style="background-color: {{ $headerBackgroundColor }}; width: 870px; margin: auto; padding: 5px 40px;">
<a href="{{ $url }}">
@if (isset($globals) && $globals->email_branding->logo->absoluteUrl()->get())
<img src="{{ $globals->email_branding->logo->absoluteUrl() }}" class="logo" alt="Logo">
@else
{!! $slot !!}
@endif
</a>
</div>
</td>
</tr>
