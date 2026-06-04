@props(['lines' => 3, 'class' => ''])

<div {{ $attributes->merge(['class' => 'animate-pulse space-y-3 '.$class]) }} aria-hidden="true">
    @for($i = 0; $i < $lines; $i++)
    <div class="h-4 bg-gray-200 rounded-lg {{ $i === 0 ? 'w-3/4' : ($i === $lines - 1 ? 'w-1/2' : 'w-full') }}"></div>
    @endfor
</div>
