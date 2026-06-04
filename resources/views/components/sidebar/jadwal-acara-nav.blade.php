@props([
    'panel' => 'admin',
    'activeMenu' => '',
    'rundownUrl' => '#',
    'meetingUrl' => '#',
    'rundownLocked' => false,
    'meetingLocked' => false,
    'lockHint' => null,
    'linkActiveClass' => 'flex items-center px-4 py-3 bg-leafSoft text-bottle font-semibold rounded-xl',
    'linkIdleClass' => 'flex items-center px-4 py-3 text-grayText hover:bg-gray-50 hover:text-bottle font-medium rounded-xl transition',
    'subActiveClass' => 'flex items-center pl-11 pr-4 py-2.5 text-sm bg-leafSoft/80 text-bottle font-semibold rounded-lg',
    'subIdleClass' => 'flex items-center pl-11 pr-4 py-2.5 text-sm text-grayText hover:bg-gray-50 hover:text-bottle rounded-lg transition',
    'subLockedClass' => 'flex items-center pl-11 pr-4 py-2.5 text-sm text-gray-400 rounded-lg cursor-not-allowed select-none',
])

@php
    $jadwalMenus = ['jadwal', 'jadwal-rundown', 'jadwal-meeting', 'vendor-meetings'];
    $isJadwalGroupOpen = in_array($activeMenu, $jadwalMenus, true);
    $isParentActive = $isJadwalGroupOpen;
    $parentBtnClass = $isParentActive ? $linkActiveClass : $linkIdleClass;
@endphp

<div class="jadwal-acara-nav" data-jadwal-nav data-initial-open="{{ $isJadwalGroupOpen ? '1' : '0' }}">
    <button type="button"
            class="{{ $parentBtnClass }} w-full text-left jadwal-acara-nav__toggle"
            aria-expanded="{{ $isJadwalGroupOpen ? 'true' : 'false' }}"
            aria-controls="jadwal-acara-submenu-{{ $panel }}">
        <svg class="w-5 h-5 mr-3 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
        </svg>
        <span class="flex-1">Jadwal Acara</span>
        <svg class="w-4 h-4 shrink-0 jadwal-acara-nav__chevron transition-transform duration-300 {{ $isJadwalGroupOpen ? 'rotate-180' : '' }}"
             fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
        </svg>
    </button>

    <div id="jadwal-acara-submenu-{{ $panel }}"
         class="jadwal-acara-nav__submenu overflow-hidden transition-[max-height] duration-300 ease-in-out {{ $isJadwalGroupOpen ? 'is-open' : '' }}"
         @if($isJadwalGroupOpen) style="max-height: 8rem;" @else style="max-height: 0;" @endif>
        <div class="mt-1 space-y-0.5 pb-1">
            @if($rundownLocked)
                <span class="{{ $subLockedClass }}" title="{{ $lockHint ?? 'Terkunci — lunasi pembayaran untuk akses rundown' }}">
                    <svg class="w-3.5 h-3.5 mr-2 shrink-0 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                    </svg>
                    Rundown Hari-H
                </span>
            @else
                <a href="{{ $rundownUrl }}"
                   class="{{ in_array($activeMenu, ['jadwal', 'jadwal-rundown'], true) ? $subActiveClass : $subIdleClass }} jadwal-acara-nav__link"
                   data-loading-message="Menyiapkan seluruh rangkaian rundown acara...">
                    Rundown Hari-H
                </a>
            @endif

            @if($meetingLocked)
                <span class="{{ $subLockedClass }}" title="{{ $lockHint ?? 'Terkunci' }}">
                    <svg class="w-3.5 h-3.5 mr-2 shrink-0 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                    </svg>
                    Jadwal Meeting Vendor
                </span>
            @else
                <a href="{{ $meetingUrl }}"
                   class="{{ in_array($activeMenu, ['jadwal-meeting', 'vendor-meetings'], true) ? $subActiveClass : $subIdleClass }} jadwal-acara-nav__link"
                   data-loading-message="Memuat jadwal meeting vendor...">
                    Jadwal Meeting Vendor
                </a>
            @endif
        </div>
    </div>
</div>

@once
    @push('scripts')
        <script src="{{ asset('js/sidebar-jadwal-acara.js') }}?v=2" defer></script>
    @endpush
@endonce
