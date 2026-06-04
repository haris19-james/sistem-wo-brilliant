@props([
    'value' => 0,
    'count' => 0,
    'size' => 'sm', // sm|md
    'color' => 'green', // green|brand
])

@php
    $v = (float) $value;
    $filled = (int) floor($v);
    $hasHalf = ($v - $filled) >= 0.5;
    $empty = 5 - $filled - ($hasHalf ? 1 : 0);

    $starClass = $size === 'md' ? 'w-4 h-4' : 'w-3.5 h-3.5';
    $textClass = $size === 'md' ? 'text-sm' : 'text-xs';
    $filledColor = ($color ?? 'green') === 'green' ? 'text-green-500' : 'text-bottleBright';
@endphp

<div class="flex items-center gap-2">
    <div class="flex items-center gap-0.5">
        @for($i=0; $i < $filled; $i++)
            <svg class="{{ $starClass }} {{ $filledColor }}" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
            </svg>
        @endfor

        @if($hasHalf)
            <svg class="{{ $starClass }} {{ $filledColor }}" viewBox="0 0 20 20" aria-hidden="true">
                <defs>
                    <linearGradient id="halfStar" x1="0" x2="1" y1="0" y2="0">
                        <stop offset="50%" stop-color="currentColor" />
                        <stop offset="50%" stop-color="#E5E7EB" />
                    </linearGradient>
                </defs>
                <path fill="url(#halfStar)" d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
            </svg>
        @endif

        @for($i=0; $i < $empty; $i++)
            <svg class="{{ $starClass }} text-gray-200" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
            </svg>
        @endfor
    </div>

    <div class="{{ $textClass }} text-gray-600">
        <span class="font-semibold text-gray-800">{{ number_format($v, 1) }}</span>
        @if((int) $count > 0)
            <span class="text-gray-400">({{ number_format((int) $count, 0, ',', '.') }})</span>
        @endif
    </div>
</div>

