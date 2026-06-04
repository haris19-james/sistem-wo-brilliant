<div id="page-nav-skeleton"
     class="fixed inset-0 z-[9998] bg-white/85 backdrop-blur-sm hidden pointer-events-none"
     aria-hidden="true">
    <div class="max-w-5xl mx-auto px-6 lg:px-8 pt-24 space-y-6 animate-pulse">
        <div class="h-8 bg-gray-200 rounded-xl w-1/3"></div>
        <div class="h-4 bg-gray-100 rounded-lg w-1/4"></div>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-8">
            @for($i = 0; $i < 3; $i++)
            <div class="h-28 bg-gray-100 rounded-2xl"></div>
            @endfor
        </div>
        <div class="h-64 bg-gray-100 rounded-2xl mt-4"></div>
        <div class="space-y-3">
            <div class="h-4 bg-gray-200 rounded w-full"></div>
            <div class="h-4 bg-gray-200 rounded w-5/6"></div>
            <div class="h-4 bg-gray-100 rounded w-2/3"></div>
        </div>
    </div>
</div>
