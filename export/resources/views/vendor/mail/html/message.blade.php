<x-mail::layout>
<x-slot:head>
@php
    $emailBranding = $globals->email_branding ?? null;
    $emailLinkColor = $emailBranding->email_link_color ?? '#4951D5';
    $footerTextColor = $emailBranding->footer_text_color ?? '#60606C';
    $footerLinkColor = $emailBranding->footer_link_color ?? '#1A1A1A';
@endphp
<style>
:root {
    --mail-link-color: {{ $emailLinkColor }};
    --mail-footer-text-color: {{ $footerTextColor }};
    --mail-footer-link-color: {{ $footerLinkColor }};
}

.body-container {
    padding: 0px 70px;
}

.header-top {
    padding: 25px 25px 0px 25px;
}

.footer-divider-mobile {
    display: none;
}

@media only screen and (max-width: 750px) {
    * {
        font-size: 14px!important;
    }

    .header-top {
        padding: 25px 0px 0px 0px!important;
    }

    .body-container {
        padding: 0px 10px!important;
    }

    .footer-container {
        padding: 20px 10px!important;
    }

    .footer-divider-mobile {
        display: block !important;
    }

    .footer-table {
        width: 100% !important;
        display: block !important;
    }
    .footer-column {
        display: block !important;
        width: 100% !important;
        max-width: 100% !important;
        padding: 10px 0 !important;
        text-align: center !important;
    }
    .footer-divider {
        display: none !important;
    }
}
</style>
</x-slot>

<div class="body-container">
{{-- Body --}}
{{ $slot }}

{{-- Subcopy --}}
@isset($subcopy)
<x-slot:subcopy>
<x-mail::subcopy>
{!! $subcopy !!}
</x-mail::subcopy>
</x-slot>
@endisset
</div>

{{-- Footer --}}
@if ($footer ?? false)
<x-slot:footer>
<x-mail::footer>
    {{ $footer }}
</x-mail::footer>
</x-slot>
@endif
</x-mail::layout>
