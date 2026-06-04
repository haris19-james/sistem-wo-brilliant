<nav class="bg-white border-b border-gray-100 px-6 py-4 flex justify-between items-center">
    <a href="{{ route('home') }}" class="font-bold text-bottle text-xl">Brilliant WO</a>
    <div class="hidden md:flex gap-6 text-sm font-medium">
        <a href="{{ route('home') }}" class="hover:text-bottle">Beranda</a>
        <a href="{{ route('paket') }}" class="hover:text-bottle">Paket</a>
        <a href="{{ route('vendor') }}" class="hover:text-bottle">Vendor</a>
        <a href="{{ route('about') }}" class="hover:text-bottle">Tentang</a>
        <a href="{{ route('contact') }}" class="hover:text-bottle">Kontak</a>
    </div>
    <div class="flex gap-3 text-sm">
        @auth
            <a href="{{ route('client.dashboard') }}" class="text-bottle font-semibold">Dashboard</a>
        @else
            <a href="{{ route('login') }}">Login</a>
            <a href="{{ route('admin.login') }}" class="text-gray-500">Admin</a>
        @endauth
    </div>
</nav>
