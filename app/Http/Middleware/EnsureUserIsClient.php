<?php

namespace App\Http\Middleware;

use App\Support\Role;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsClient
{
    public function handle(Request $request, Closure $next): Response
    {
        $role = $request->user()?->role;

        if ($role === Role::ADMIN) {
            return redirect()->route('admin.dashboard');
        }

        if ($role === Role::LAPANGAN) {
            return redirect()->route('lapangan.dashboard');
        }

        if (! Role::isClient($role)) {
            return redirect()->route('login');
        }

        return $next($request);
    }
}
