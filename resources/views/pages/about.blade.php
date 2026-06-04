@extends('layouts.public')

@section('title', 'Tentang Kami — Brilliant WO')

@section('content')
<section class="relative py-14 lg:py-20 overflow-hidden">
    <div class="absolute inset-0 z-0 hidden lg:block">
        <div class="absolute right-0 top-0 w-[55%] h-full">
            <img src="{{ config('brilliant.hero_image') }}" alt="Wedding" class="w-full h-full object-cover" style="mask-image: linear-gradient(to right, transparent, black 50%); -webkit-mask-image: linear-gradient(to right, transparent, black 50%);">
        </div>
    </div>
    <div class="container mx-auto px-6 relative z-10">
        <div class="max-w-xl text-center lg:text-left">
            <h1 class="text-4xl md:text-5xl font-serif font-bold text-gray-900 leading-tight">
                Tentang <span class="text-bottle">Kami</span>
            </h1>
            <p class="text-2xl font-serif font-bold text-bottle mt-2">Brilliant Wedding Organizer</p>
            <p class="text-lg text-gray-600 mt-6 leading-relaxed">
                Brilliant hadir untuk membantu setiap pasangan mewujudkan pernikahan impian dengan pelayanan terbaik, penuh makna, dan tak terlupakan.
            </p>
        </div>
        <div class="mt-10 lg:hidden rounded-3xl overflow-hidden shadow-lg h-64">
            <img src="{{ config('brilliant.hero_image') }}" alt="Wedding" class="w-full h-full object-cover">
        </div>
    </div>
</section>

<section class="container mx-auto px-6 py-16">
    <div class="flex flex-col lg:flex-row gap-12">
        <div class="lg:w-1/2 flex flex-col md:flex-row gap-6">
            <img src="https://images.unsplash.com/photo-1522673607200-164d1b6ce486?w=600&q=80" alt="Cerita kami" class="w-full md:w-1/2 h-64 md:h-auto rounded-3xl object-cover shadow-md">
            <div class="flex flex-col justify-center">
                <h2 class="text-2xl font-serif font-bold text-bottle mb-4">Cerita Kami</h2>
                <div class="space-y-3 text-sm text-gray-600 leading-relaxed">
                    <p>Brilliant berawal dari passion merancang momen indah yang berkesan. Setiap cinta punya cerita unik yang layak dirayakan.</p>
                    <p>Dari konsep hingga hari bahagia, kami memastikan setiap detail ditangani dengan sempurna.</p>
                </div>
            </div>
        </div>
        <div class="lg:w-1/2 bg-grayBox rounded-3xl p-8">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                @foreach([
                    ['Profesional & Berpengalaman', 'Tim berpengalaman di bidang event & wedding.'],
                    ['Vendor Terpercaya', 'Hanya mitra vendor terpilih dan berkualitas.'],
                    ['Harga Transparan', 'Rincian jelas tanpa biaya tersembunyi.'],
                    ['Pelayanan Personal', 'Kami hadir untuk kebutuhan Anda.'],
                ] as [$title, $desc])
                <div class="bg-white p-5 rounded-2xl shadow-sm">
                    <h3 class="font-bold text-gray-900 mb-1 text-sm">{{ $title }}</h3>
                    <p class="text-xs text-gray-500">{{ $desc }}</p>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</section>

<section class="container mx-auto px-6 pb-12">
    <div class="bg-grayBox rounded-3xl p-8 lg:p-12 grid grid-cols-2 lg:grid-cols-4 gap-8 text-center">
        <div>
            <p class="text-4xl font-serif font-bold text-bottle">{{ config('brilliant.stats.events') }}</p>
            <p class="text-xs text-gray-500 uppercase mt-1">Acara Berhasil</p>
        </div>
        <div>
            <p class="text-4xl font-serif font-bold text-bottle">{{ config('brilliant.stats.vendors') }}</p>
            <p class="text-xs text-gray-500 uppercase mt-1">Vendor Terpercaya</p>
        </div>
        <div>
            <p class="text-4xl font-serif font-bold text-bottle">{{ config('brilliant.stats.years') }}</p>
            <p class="text-xs text-gray-500 uppercase mt-1">Tahun Pengalaman</p>
        </div>
        <div>
            <p class="text-4xl font-serif font-bold text-bottle">{{ config('brilliant.stats.rating') }}</p>
            <p class="text-xs text-gray-500 uppercase mt-1">Rating Pelanggan</p>
        </div>
    </div>
</section>

<section class="container mx-auto px-6 py-12">
    <h2 class="text-2xl font-serif font-bold text-bottle text-center mb-8">Momen yang Kami Wujudkan</h2>
    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-3">
        @foreach([
            'https://images.unsplash.com/photo-1519225421980-715cb0215aed?w=400&q=80',
            'https://images.unsplash.com/photo-1522673607200-164d1b6ce486?w=400&q=80',
            'https://images.unsplash.com/photo-1469334031218-e382a71b716b?w=400&q=80',
            'https://images.unsplash.com/photo-1464366400600-7168b8af9bc3?w=400&q=80',
            'https://images.unsplash.com/photo-1544078751-58fee2d8a03b?w=400&q=80',
            'https://images.unsplash.com/photo-1478146896981-b80fe463b330?w=400&q=80',
        ] as $img)
        <a href="{{ route('blog') }}" class="block rounded-2xl overflow-hidden hover:opacity-90 transition">
            <img src="{{ $img }}" alt="Galeri" class="w-full h-32 md:h-40 object-cover">
        </a>
        @endforeach
    </div>
</section>

@include('pages.partials.cta-consult')
@endsection
