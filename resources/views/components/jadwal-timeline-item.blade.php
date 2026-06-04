@props([
    'item' => [],
    'pesanan' => null,
    'panel' => 'lapangan',
])

@php
    use App\Services\ScheduleAccessService;

    $locked = $pesanan && ! ScheduleAccessService::canAccessTimelineItem($pesanan, $item);
    $lockLabel = $locked ? ScheduleAccessService::lockLabel($pesanan) : null;
@endphp

<div @class([
    'relative pb-8 last:pb-0',
    'opacity-60' => $locked,
])>
    <span @class([
        'absolute -left-8 top-1 flex items-center justify-center w-6 h-6 rounded-full bg-white border-2 text-xs shadow-sm',
        'border-gray-300 text-gray-400' => $locked,
        'border-green-400 text-green-600' => ! $locked,
    ])>
        @if($locked)
        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
        @elseif(($item['agenda_type'] ?? '') === 'technical_meeting')
        <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path d="M10 2a8 8 0 100 16 8 8 0 000-16zm1 11H9V9h2v4zm0-5H9V6h2v2z"/></svg>
        @else
        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
        @endif
    </span>

    <div class="min-w-0">
        <p class="text-xs font-medium text-gray-500">
            {{ $item['date_label'] ?? '' }}
            @if(! empty($item['time_label']))
            <span class="text-gray-400">·</span> {{ $item['time_label'] }}
            @endif
        </p>
        <p class="text-sm font-bold text-gray-900 mt-1 leading-snug">{{ $item['title'] ?? 'Agenda' }}</p>

        <div class="flex flex-wrap items-center gap-2 mt-2">
            @if(! empty($item['badge']))
            <span class="inline-flex px-2.5 py-0.5 rounded-full text-[11px] font-semibold {{ $item['badge_class'] ?? 'bg-gray-100 text-gray-600' }}">
                {{ $item['badge'] }}
            </span>
            @endif

            @if($locked && $lockLabel)
            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-gray-100 text-gray-600 border border-gray-200">
                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                {{ $lockLabel }}
            </span>
            @endif

            @if(! empty($item['checklist_status']) && ! $locked)
            <span class="text-[11px] text-gray-500 font-medium">{{ $item['checklist_status'] }}</span>
            @endif
        </div>
    </div>
</div>
