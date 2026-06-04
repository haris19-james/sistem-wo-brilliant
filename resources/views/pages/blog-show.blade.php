@extends('layouts.public')

@section('title', $post['title'].' — Blog Brilliant WO')
@section('meta_description', $post['excerpt'])

@section('content')
<article class="container mx-auto px-6 py-10 max-w-3xl">
    <nav class="text-sm text-gray-500 mb-6 flex flex-wrap gap-2">
        <a href="{{ route('home') }}" class="hover:text-bottle">Beranda</a>
        <span>›</span>
        <a href="{{ route('blog') }}" class="hover:text-bottle">Blog</a>
        <span>›</span>
        <span class="text-gray-800">{{ $post['category_label'] }}</span>
    </nav>

    <span class="text-xs font-semibold text-bottle uppercase tracking-wide">{{ $post['category_label'] }}</span>
    <h1 class="text-3xl md:text-4xl font-bold text-gray-900 mt-2 mb-4 leading-tight">{{ $post['title'] }}</h1>
    <div class="flex flex-wrap gap-4 text-sm text-gray-500 mb-8 pb-6 border-b border-gray-100">
        <span>{{ $post['date_formatted'] }}</span>
        <span>{{ $post['read_time'] }}</span>
        <span>Oleh {{ $post['author'] }}</span>
    </div>

    <img src="{{ $post['image'] }}" alt="{{ $post['title'] }}" class="w-full h-64 md:h-80 object-cover rounded-2xl shadow-lg mb-8">

    <div class="prose prose-green max-w-none space-y-4 text-gray-700 leading-relaxed">
        <p class="text-lg font-medium text-gray-800">{{ $post['excerpt'] }}</p>
        @foreach($post['body'] as $paragraph)
        <p>{{ $paragraph }}</p>
        @endforeach
    </div>

    <div class="mt-10 flex flex-col sm:flex-row gap-3">
        <a href="{{ route('contact') }}" class="inline-flex justify-center bg-bottle text-white font-semibold py-3 px-8 rounded-xl hover:bg-bottleHover">Konsultasi Gratis</a>
        <a href="{{ route('paket') }}" class="inline-flex justify-center border border-bottle text-bottle font-semibold py-3 px-8 rounded-xl hover:bg-leafSoft">Lihat Paket</a>
    </div>
</article>

@if(count($related) > 0)
<section class="bg-gray-50 py-12">
    <div class="container mx-auto px-6">
        <h2 class="text-xl font-bold text-gray-900 mb-6">Artikel Terkait</h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            @foreach($related as $rel)
                @include('pages.partials.blog-card', ['post' => $rel])
            @endforeach
        </div>
    </div>
</section>
@endif
@endsection
