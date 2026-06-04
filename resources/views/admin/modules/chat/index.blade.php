@extends('layouts.admin')

@section('title', 'Chat')
@section('page-title', 'Panel Chat')
@section('page-subtitle', 'Komunikasi customer per booking — konteks ID booking wajib')

@section('content')
<x-chat.booking-workspace
    panel="admin"
    :threads="$threads"
    :filter="$filter"
    :selected-pesanan-id="$selectedPesananId"
    :detail="$detail"
/>
@endsection

@push('scripts')
<script src="{{ asset('js/booking-chat.js') }}?v=1"></script>
@endpush
