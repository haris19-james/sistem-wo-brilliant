@extends('layouts.public')

@section('title', 'Blog & Inspirasi — Brilliant WO')

@section('content')
<section class="container mx-auto px-6 pt-10 pb-6">
    <h1 class="text-4xl md:text-5xl font-bold text-gray-900 mb-3">Blog & <span class="text-bottle">Inspirasi</span></h1>
    <p class="text-lg text-gray-600 max-w-2xl">Tips, inspirasi, dan cerita pernikahan dari tim Brilliant WO.</p>
</section>

<section class="container mx-auto px-6 mb-10">
    <div class="flex overflow-x-auto gap-4 border-b border-gray-200 pb-2 text-sm font-medium scrollbar-hide">
        @foreach($categories as $slug => $label)
        <a href="{{ route('blog', $slug === 'semua' ? [] : ['kategori' => $slug]) }}"
           class="whitespace-nowrap pb-2 border-b-2 transition {{ $activeCategory === $slug ? 'text-bottle border-bottle' : 'text-gray-500 border-transparent hover:text-bottle' }}">
            {{ $label }}
        </a>
        @endforeach
    </div>
</section>

<section class="container mx-auto px-6 pb-16">
    @if(count($posts) > 0)
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
        @foreach($posts as $post)
            @include('pages.partials.blog-card', ['post' => $post])
        @endforeach
    </div>
    @else
    <div class="text-center py-16 bg-gray-50 rounded-2xl">
        <p class="text-gray-500 mb-4">Belum ada artikel di kategori ini.</p>
        <a href="{{ route('blog') }}" class="text-bottle font-semibold hover:underline">Lihat semua artikel</a>
    </div>
    @endif
</section>

@include('pages.partials.cta-consult')
@endsection
