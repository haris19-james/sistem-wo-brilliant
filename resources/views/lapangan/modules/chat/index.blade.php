@extends('layouts.lapangan')

@section('title', 'Chat / Pesan')

@section('content')
<div class="px-4 sm:px-6 py-6">
    <x-chat.booking-workspace
        panel="lapangan"
        :threads="$threads"
        :filter="$filter"
        :selected-pesanan-id="$selectedPesananId"
        :detail="$detail"
    />
</div>
@endsection

@push('scripts')
<script src="{{ asset('js/booking-chat.js') }}?v=1"></script>
@endpush
