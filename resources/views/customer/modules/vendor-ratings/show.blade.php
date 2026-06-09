@extends('layouts.customer')

@section('title', 'Beri Rating Vendor')
@section('page-title', 'BERI RATING VENDOR')
@section('page-subtitle', '')

@section('content')
<div class="max-w-4xl opacity-0 translate-y-2 transition-all duration-500 ease-out" x-data="{ init() { this.$el.classList.remove('opacity-0', 'translate-y-2'); this.$el.classList.add('opacity-100', 'translate-y-0'); } }">
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <h2 class="text-lg font-medium text-gray-800 mb-6 border-b border-gray-100 pb-4">
            Berikan Ulasan Anda - Pernikahan {{ $pesanan->nama_pasangan }} (Booking #{{ $pesanan->nomor_pesanan }})
        </h2>

        @if($errors->any())
            <div class="mb-6 p-4 bg-red-50 text-red-800 border border-red-200 rounded-xl text-sm">
                <ul class="list-disc list-inside">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('client.vendor-ratings.store-bulk', $pesanan->id) }}" method="POST">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-8">
                {{-- Vendors Review --}}
                @foreach($pesanan->vendors as $vendor)
                    @if(!in_array($vendor->id, $reviewedVendorIds))
                    <div class="bg-gray-50 rounded-lg p-5 border border-gray-200" x-data="{ rating: 0, hover: 0 }">
                        <input type="hidden" name="vendors[{{ $vendor->id }}][rating]" x-model="rating" required>
                        <h3 class="font-bold text-gray-900 text-sm uppercase mb-1">**{{ strtoupper($vendor->kategori->nama ?? 'VENDOR') }}** <span class="text-gray-600 capitalize font-normal">({{ $vendor->nama_vendor }})</span></h3>
                        
                        <div class="flex gap-1 mb-3">
                            <template x-for="i in 5">
                                <button type="button" 
                                    @click="rating = i" 
                                    @mouseover="hover = i" 
                                    @mouseleave="hover = 0"
                                    class="focus:outline-none transition-transform hover:scale-110">
                                    <svg class="w-8 h-8" :class="(hover >= i || rating >= i) ? 'text-yellow-400' : 'text-gray-300'" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                    </svg>
                                </button>
                            </template>
                        </div>
                        
                        <textarea name="vendors[{{ $vendor->id }}][ulasan]" rows="3" class="w-full text-sm border border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500/20 p-3 bg-white" placeholder="Tuliskan ulasan Anda untuk {{ strtolower($vendor->kategori->nama ?? 'vendor') }}..."></textarea>
                        
                        <div class="mt-3">
                            <button type="button" class="inline-flex items-center gap-2 px-3 py-2 bg-white border border-gray-300 rounded-md text-xs font-semibold text-gray-700 hover:bg-gray-50 transition">
                                <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                Unggah Foto (Optional)
                            </button>
                        </div>
                    </div>
                    @endif
                @endforeach

                {{-- Event Planner Review --}}
                @if(!$hasReviewedWo)
                <div class="bg-gray-50 rounded-lg p-5 border border-gray-200" x-data="{ rating: 0, hover: 0 }">
                    <input type="hidden" name="wo_rating" x-model="rating" required>
                    <h3 class="font-bold text-gray-900 text-sm uppercase mb-1">**EVENT PLANNER** <span class="text-gray-600 capitalize font-normal">(HARIS WO)</span></h3>
                    
                    <div class="flex gap-1 mb-3">
                        <template x-for="i in 5">
                            <button type="button" 
                                @click="rating = i" 
                                @mouseover="hover = i" 
                                @mouseleave="hover = 0"
                                class="focus:outline-none transition-transform hover:scale-110">
                                <svg class="w-8 h-8" :class="(hover >= i || rating >= i) ? 'text-yellow-400' : 'text-gray-300'" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                </svg>
                            </button>
                        </template>
                    </div>
                    
                    <textarea name="wo_ulasan" rows="3" class="w-full text-sm border border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500/20 p-3 bg-white" placeholder="Tuliskan ulasan Anda untuk kami..."></textarea>
                </div>
                @endif
            </div>

            <button type="submit" class="w-full py-3 bg-blue-600 text-white font-bold rounded-lg hover:bg-blue-700 transition">
                SIMPAN ULASAN
            </button>
            
            <div class="mt-4 flex items-center justify-start gap-2">
                <input type="checkbox" name="publish_consent" id="publish_consent" value="1" checked class="rounded border-gray-300 text-blue-600 shadow-sm focus:ring-blue-500">
                <label for="publish_consent" class="text-sm text-gray-700">Saya setuju ulasan saya dipublikasikan di halaman utama.</label>
            </div>
        </form>
    </div>
</div>
@endsection
