@props(['nextEvent' => null])

@if($nextEvent)
    @php
        $eventDate = \Carbon\Carbon::parse($nextEvent->tanggal_acara . ' ' . ($nextEvent->jam_acara ?? '00:00'));
        $isPast = $eventDate->isPast();
    @endphp

    <div class="bg-white dark:bg-gray-800 border rounded-lg p-4 shadow-sm">
        <div class="flex items-start justify-between">
            <div>
                <h3 class="text-sm font-medium text-gray-900 dark:text-gray-100">Upcoming Schedule</h3>
                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Next event from your vendor</p>
            </div>
            <div>
                @if($isPast)
                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs bg-green-100 text-green-800">Completed</span>
                @endif
            </div>
        </div>

        <div class="mt-3 grid grid-cols-1 gap-2">
            <div class="flex items-center justify-between">
                <div>
                    <div class="text-sm font-semibold text-gray-800 dark:text-gray-100">{{ $nextEvent->vendor_nama ?? ($nextEvent->vendor->name ?? 'Vendor') }}</div>
                    <div class="text-xs text-gray-500 dark:text-gray-400">{{ $eventDate->translatedFormat('l, j F Y') }} — {{ $eventDate->format('H:i') }}</div>
                </div>
                <div class="flex items-center space-x-2">
                    <a href="{{ route('calendar.download', ['pesanan' => $nextEvent->id]) }}" class="inline-flex items-center px-3 py-1.5 bg-indigo-600 text-white text-xs rounded-md hover:bg-indigo-700">Add to Calendar</a>
                </div>
            </div>
            @if($nextEvent->lokasi ?? false)
                <div class="text-xs text-gray-500 dark:text-gray-400">Location: {{ $nextEvent->lokasi }}</div>
            @endif
        </div>
    </div>
@else
    <div class="bg-white dark:bg-gray-800 border rounded-lg p-4 shadow-sm text-center text-sm text-gray-500">No upcoming schedules</div>
@endif
