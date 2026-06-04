<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'admin' => \App\Http\Middleware\EnsureUserIsAdmin::class,
            'client' => \App\Http\Middleware\EnsureUserIsClient::class,
            'customer' => \App\Http\Middleware\EnsureUserIsClient::class,
            'lapangan' => \App\Http\Middleware\EnsureUserIsLapangan::class,
            'schedule.access' => \App\Http\Middleware\EnsureScheduleAccess::class,
            'payment.deadline' => \App\Http\Middleware\CheckPaymentDeadline::class,
        ]);

        $middleware->redirectGuestsTo(function (Request $request) {
            if ($request->is('admin', 'admin/*')) {
                return route('admin.login');
            }
            if ($request->is('lapangan', 'lapangan/*')) {
                return route('lapangan.login');
            }

            return route('login');
        });

        $middleware->redirectUsersTo(function (Request $request) {
            return match ($request->user()?->role) {
                'admin' => route('admin.dashboard'),
                'lapangan' => route('lapangan.dashboard'),
                default => route('client.dashboard'),
            };
        });
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
