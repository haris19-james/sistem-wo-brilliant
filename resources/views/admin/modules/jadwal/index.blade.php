@extends('layouts.admin')

@section('title', 'Jadwal Acara')
@section('page-title', 'Panel Admin')
@section('page-subtitle', '')

@section('content')
<x-jadwal-terpadu
    :panel="$panel"
    :pesanans="$pesanans"
    :pesanan="$pesanan"
    :main-event="$mainEvent"
    :timeline-items="$timelineItems"
    :kua-checklist="$kuaChecklist"
    :has-korlap="$hasKorlap"
    :can-add-vendor-meeting="$canAddVendorMeeting"
/>
@endsection
