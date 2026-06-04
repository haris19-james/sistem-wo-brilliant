@props([
    'banner' => [],
])

@php
    $type = $banner['type'] ?? 'warning';
    $classes = match ($type) {
        'success' => 'bg-green-50 border-green-300 text-green-900',
        'info' => 'bg-blue-50 border-blue-300 text-blue-900',
        'overdue' => 'bg-red-50 border-red-500 text-red-900 ring-2 ring-red-200',
        'deadline_warning' => 'bg-orange-100 border-orange-500 text-orange-950',
        default => 'bg-orange-50 border-orange-300 text-orange-900',
    };
    $iconBg = match ($type) {
        'success' => 'bg-green-100 text-green-700',
        'info' => 'bg-blue-100 text-blue-700',
        'overdue' => 'bg-red-200 text-red-800 animate-pulse',
        'deadline_warning' => 'bg-orange-200 text-orange-900',
        default => 'bg-orange-100 text-orange-700',
    };
    $borderAccent = match ($type) {
        'overdue' => 'border-l-red-600',
        'deadline_warning' => 'border-l-orange-600',
        default => '',
    };
@endphp

<div {{ $attributes->merge(['class' => "rounded-2xl border-l-4 p-5 shadow-sm {$classes} {$borderAccent}"]) }}>
    <div class="flex gap-4 items-start">
        <span class="flex-shrink-0 w-11 h-11 rounded-xl flex items-center justify-center text-xl font-bold {{ $iconBg }}">
            {{ $banner['icon'] ?? 'ℹ️' }}
        </span>
        <div class="min-w-0 flex-1">
            <p @class([
                'text-sm sm:text-base leading-relaxed',
                'font-bold' => in_array($type, ['overdue', 'deadline_warning'], true),
                'font-semibold' => ! in_array($type, ['overdue', 'deadline_warning'], true),
            ])>{{ $banner['message'] ?? '' }}</p>
            @if(! empty($banner['submessage']))
            <p class="text-xs sm:text-sm mt-2 opacity-90 flex items-center gap-1.5">
                <svg class="w-4 h-4 shrink-0 animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                {{ $banner['submessage'] }}
            </p>
            @endif
        </div>
    </div>
</div>
