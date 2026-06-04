<?php

namespace App\Providers;

use App\Models\ItemTambahan;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use App\Events\BookingCompleted;
use App\Listeners\SendVendorReviewReminder;
use App\Models\Review;
use App\Models\Pesanan;
use App\Observers\PesananObserver;
use App\Observers\ReviewObserver;
use Illuminate\Support\Facades\Event;
use App\Policies\ReviewPolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\View;
use App\View\Composers\CustomerChatNavComposer;
use App\View\Composers\NotificationBellComposer;
use App\View\Composers\SidebarJadwalNavComposer;
use Illuminate\Support\Facades\Blade;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Gate::policy(Review::class, ReviewPolicy::class);

        // ✅ Register PesananObserver untuk trigger agenda generation otomatis
        Pesanan::observe(PesananObserver::class);
        Review::observe(ReviewObserver::class);

        Event::listen(BookingCompleted::class, SendVendorReviewReminder::class);

        View::composer(['layouts.client', 'layouts.customer', 'layouts.lapangan'], SidebarJadwalNavComposer::class);
        View::composer(['layouts.client', 'layouts.customer'], CustomerChatNavComposer::class);
        View::composer(['layouts.admin', 'layouts.lapangan', 'layouts.client', 'layouts.customer'], NotificationBellComposer::class);

        Route::bind('addon', fn (string $value) => ItemTambahan::findOrFail($value));
        // Register dashboard header component for simple tag usage <x-dashboard-header />
        Blade::component('components.dashboard.header', 'dashboard-header');
    }
}
