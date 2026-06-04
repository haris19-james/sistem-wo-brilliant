@props([
    'slides' => null,
    'interval' => null,
])

@php
    $slides = $slides ?? \App\Support\Branding::heroGallerySlides();
    $interval = $interval ?? config('brilliant.hero_carousel_interval_ms', 4000);
    $slideCount = count($slides);
@endphp

<div {{ $attributes->merge(['class' => 'organic-frame relative shadow-2xl border-8 border-white']) }}
     x-data="heroPortfolioCarousel({{ $slideCount }}, {{ (int) $interval }})"
     x-init="init()"
     @mouseenter="pause()"
     @mouseleave="resume()"
     @focusin="pause()"
     @focusout="resume()"
     role="region"
     aria-roledescription="carousel"
     aria-label="Galeri portofolio Brilliant WO">

    {{-- Badge galeri --}}
    <div class="absolute top-4 left-4 z-20 flex items-center gap-1.5 rounded-full bg-white/90 backdrop-blur-sm px-3 py-1.5 text-[0.65rem] font-bold uppercase tracking-wider text-bottle shadow-sm pointer-events-none">
        <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
            <path d="M21 19V5a2 2 0 00-2-2H5a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2zM8.5 13.5l2.5 3.01L14.5 12l4.5 6H5l3.5-4.5z"/>
        </svg>
        Galeri Portofolio
    </div>

    <div class="hero-carousel-viewport relative w-full h-full overflow-hidden bg-leafSoft">
        @foreach($slides as $index => $slide)
        <div class="hero-carousel-slide absolute inset-0 transition-opacity duration-1000 ease-in-out"
             :class="active === {{ $index }} ? 'opacity-100 z-[1]' : 'opacity-0 z-0'"
             :aria-hidden="active !== {{ $index }} ? 'true' : 'false'">
            <img src="{{ $slide['src'] }}"
                 alt="{{ $slide['alt'] }}"
                 class="w-full h-full object-cover"
                 width="840"
                 height="840"
                 @if($index === 0) loading="eager" fetchpriority="high" @else loading="lazy" @endif
                 decoding="async">
            @if(!empty($slide['caption']))
            <div class="absolute inset-x-0 bottom-0 z-10 bg-gradient-to-t from-ink/70 via-ink/30 to-transparent px-4 pb-4 pt-10 pointer-events-none transition-opacity duration-700"
                 :class="active === {{ $index }} ? 'opacity-100' : 'opacity-0'">
                <p class="text-white text-xs md:text-sm font-medium tracking-wide">{{ $slide['caption'] }}</p>
            </div>
            @endif
        </div>
        @endforeach
    </div>

    @if($slideCount > 1)
    <div class="absolute bottom-3 left-0 right-0 z-20 flex justify-center gap-1.5" role="tablist" aria-label="Pilih foto portofolio">
        @foreach($slides as $index => $slide)
        <button type="button"
                class="hero-carousel-dot w-2 h-2 rounded-full transition-all duration-300 focus:outline-none focus-visible:ring-2 focus-visible:ring-bottle focus-visible:ring-offset-2"
                :class="active === {{ $index }} ? 'bg-white w-5' : 'bg-white/50 hover:bg-white/80'"
                @click="goTo({{ $index }}); pause(); resume()"
                :aria-selected="active === {{ $index }}"
                role="tab"
                aria-label="Foto {{ $index + 1 }}: {{ $slide['alt'] }}"></button>
        @endforeach
    </div>
    @endif
</div>
