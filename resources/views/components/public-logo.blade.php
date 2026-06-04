@php
    $logoUrl = \App\Support\Branding::logoUrl();
    $size = $size ?? 'md';
    $imgClass = match($size) {
        'sm' => 'h-14 w-auto max-w-[260px]',
        'lg' => 'h-20 md:h-24 w-auto max-w-[400px]',
        default => 'h-16 sm:h-[4.25rem] md:h-20 w-auto max-w-[300px] sm:max-w-[340px] md:max-w-[380px]',
    };
@endphp
<a href="{{ route('home') }}" class="inline-flex items-center group shrink-0" aria-label="Brilliant Event & Wedding Organizer — Beranda">
    @if($logoUrl)
        <img
            src="{{ $logoUrl }}"
            alt="Brilliant Event & Wedding Organizer"
            class="{{ $imgClass }} object-contain object-left"
            width="380"
            height="120"
            decoding="async"
        >
    @else
        <div class="flex items-center gap-3">
            <div class="h-11 w-11 rounded-xl bg-leafSoft flex items-center justify-center text-bottle font-bold text-lg">B</div>
            <div class="leading-tight hidden sm:block">
                <span class="text-xl font-bold text-gray-900 tracking-tight group-hover:text-bottle transition">{{ config('brilliant.name') }}</span>
                <p class="text-[0.55rem] text-gray-600 font-medium tracking-widest uppercase">{{ config('brilliant.tagline') }}</p>
            </div>
        </div>
    @endif
</a>
