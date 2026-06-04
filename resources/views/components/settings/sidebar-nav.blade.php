@props([
    'items',
    'active',
    'variant' => 'client',
])

<nav class="space-y-1">
    @foreach($items as $item)
        @php
            $isActive = $active === $item['key'];
            $activeCustomer = $isActive ? 'rounded-2xl bg-green-50 px-4 py-3 text-green-700 shadow-sm' : 'rounded-2xl px-4 py-3 text-slate-600 hover:bg-green-50/50';
            $activeLapangan = $isActive ? 'bg-green-50 text-green-700' : 'text-gray-600 hover:text-gray-900 hover:bg-green-50/50';
        @endphp
        <a href="{{ $item['url'] }}"
           class="flex items-center gap-3 text-sm font-medium transition {{ $variant === 'client' ? $activeCustomer : 'px-3 py-2 rounded-lg '.$activeLapangan }}">
            @if($variant === 'client')
                <span class="inline-flex h-11 w-11 shrink-0 items-center justify-center rounded-2xl {{ $isActive ? 'bg-green-100 text-green-700' : 'bg-slate-100 text-slate-500' }}">
                    {{ $item['abbr'] }}
                </span>
            @else
                <i data-feather="{{ $item['icon'] }}" class="w-4 h-4 mr-1 shrink-0"></i>
            @endif
            <span>{{ $item['label'] }}</span>
        </a>
    @endforeach
</nav>
