<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Login - Brilliant WO')</title>
    @stack('head')
    <script src="https://cdn.tailwindcss.com"></script>
    @include('partials.brand-tailwind', ['extraColors' => [
        'field' => config('brilliant.colors.bottle'),
        'fieldHover' => config('brilliant.colors.bottle_hover'),
    ]])
</head>
<body class="min-h-screen flex items-center justify-center p-6 bg-gradient-to-br from-bottle via-bottleHover to-ink" data-brilliant-panel="auth">
    <div class="w-full max-w-md bg-white rounded-2xl shadow-xl shadow-black/10 p-8 md:p-10 ring-1 ring-white/20">
        <div class="text-center mb-6 md:mb-8">
            <x-public-logo size="lg" />
            @hasSection('role_label')
            <p class="text-sm text-bottle font-semibold uppercase tracking-wider mt-4">@yield('role_label')</p>
            @endif
            @hasSection('subtitle')
            <p class="text-xs text-gray-500 mt-1.5 max-w-xs mx-auto leading-relaxed">@yield('subtitle')</p>
            @endif
        </div>

        @if ($errors->any())
        <div class="mb-5 p-3.5 bg-red-50 border border-red-100 text-red-700 text-sm rounded-xl" role="alert">
            {{ $errors->first() }}
        </div>
        @endif

        @yield('content')

        @hasSection('footer')
        <div class="mt-6 md:mt-8 pt-5 border-t border-gray-100 space-y-2 text-sm text-center text-gray-500">
            @yield('footer')
        </div>
        @endif
    </div>

    @stack('scripts')
</body>
</html>
