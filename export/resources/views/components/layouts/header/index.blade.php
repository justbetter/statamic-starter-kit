@props(['logo' => null])
<header>
    <div class="flex relative z-10 md:container">
        <div class="flex flex-1 items-center justify-between md:bg-white md:py-8">
            @if ($logo)
                <a href="/" class="max-md:px-8 max-md:h-[85px] max-md:bg-white max-md:rounded-b max-md:border max-md:py-6 max-md:flex max-md:items-center max-md:justify-center max-md:basis-1/2 max-md:order-2">
                    <div class="flex flex-1 max-md:max-w-44 max-md:max-h-12 md:w-48 *:flex *:flex-1">
                        <img src="{{ $logo->absoluteUrl() }}" alt="JustBetter logo" class="flex flex-1 max-w-[100px]">
                    </div>
                </a>
            @endif
            @php($mainNavigation = Statamic::tag('nav:header_main_navigation')->fetch())
            @if($mainNavigation ?? false)
                <div class="flex gap-2 px-4">
                    @foreach ($mainNavigation as $item)
                        <a href="{{ $item['url']?->value() }}" target="_self">
                            {{ $item['title'] ?? '' }}
                        </a>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</header>
