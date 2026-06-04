@props([
    'eventDate' => null,
    'pesanan' => null,
])

@php
    use App\Support\EventCountdown;

    $date = $eventDate;
    if ($date === null && $pesanan) {
        $date = $pesanan->tanggal_acara;
    }

    $countdown = EventCountdown::badgeForEventDate($date);
@endphp

@if($countdown)
<span {{ $attributes->merge([
    'class' => 'inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-black tracking-wide '.$countdown['class'],
    'title' => $countdown['title'],
]) }}>
    {{ $countdown['label'] }}
</span>
@endif
