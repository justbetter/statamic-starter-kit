<!doctype html>
<html lang="nl" class="scroll-smooth">
    <head>
        @includeIf('layouts.seo.head')
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png">
        <link rel="icon" type="image/png" sizes="32x32" href="/groot-favicon-32x32.png">
        <link rel="icon" type="image/png" sizes="16x16" href="/favicon-16x16.png">
        <link rel="manifest" href="/site.webmanifest">
        <link rel="mask-icon" href="/safari-pinned-tab.svg" color="#000000">
        <meta name="msapplication-TileColor" content="#da532c">
        <meta name="theme-color" content="#0E1F30">

        @vite('resources/css/site.css')

        @include('statamic-glide-directive::partials.head')
        @includeIf('layouts.structured-data.head')
    </head>
    <body class="flex flex-col antialiased font-sans">
        <x-layouts.header :logo="$brand->logo ?? null" />
        @yield('content')
        <x-layouts.footer :logo="$brand->logo ?? null" />
        @includeIf('layouts.seo.footer')
        @vite('resources/js/site.js')
    </body>
</html>
