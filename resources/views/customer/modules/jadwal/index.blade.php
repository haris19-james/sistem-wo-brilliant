@extends('layouts.customer')

@section('title', 'Jadwal Acara')
@section('page-title', 'Jadwal Acara')
@section('page-subtitle', 'Kalender & agenda persiapan pernikahan')

@section('content')
<x-jadwal-terpadu
    :panel="$panel"
    :pesanans="$pesanans"
    :pesanan="$pesanan"
    :main-event="$mainEvent"
    :timeline-items="$timelineItems"
    :kua-checklist="$kuaChecklist"
    :has-korlap="$hasKorlap"
/>
@endsection
