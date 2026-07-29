@php
    $emailBranding = $globals->email_branding ?? null;
    $footerBackgroundColor = $emailBranding->footer_background_color ?? '#F7F7F7';
    $footerBottomBackgroundColor = $emailBranding->footer_bottom_background_color ?? '#4951D5';
    $footerDividerColor = $emailBranding->footer_divider_color ?? '#E0E0E0';
@endphp
<tr>
<td class="header">
<div style="width: 870px; margin: auto; padding: 20px 70px; background-color: {{ $footerBackgroundColor }};" class="footer-container">
<table class="footer-table" style="width: 100%; table-layout: fixed;">
@if($slot->isNotEmpty())
<tr>
<td align="center" colspan="3">
{{ Illuminate\Mail\Markdown::parse($slot) }}
</td>
</tr>
@endif
<tr>
{{-- Left --}}
<td class="footer-column" style="max-width: 50%; padding-right: 30px; vertical-align: top; text-align: left;">
@if($globals->email_branding->footer_left_text ?? false)
{!! $globals->email_branding->footer_left_text !!}
@endif
</td>

{{-- Middle divider --}}
<td class="footer-divider" style="width: 1px; height: 100%; border-right: 1px solid {{ $footerDividerColor }};">
</td>

{{-- Right --}}
<td class="footer-column" style="max-width: 50%; padding-left: 30px; vertical-align: top; text-align: left;">
<div style="width: 100%; height: 1px; background-color: {{ $footerDividerColor }}; margin-bottom: 25px;" class="footer-divider-mobile"></div>
@if($globals->email_branding->footer_right_text ?? false)
{!! $globals->email_branding->footer_right_text !!}
@endif
</div>
</td>
</tr>
</table>
</div>

<div style="background-color: {{ $footerBottomBackgroundColor }}; width: 870px; margin: auto; padding: 15px 40px;">
</div>
</td>
</tr>
