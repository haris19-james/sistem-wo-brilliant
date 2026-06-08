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

        if (! $request->user()) {
            return redirect()->route('login');
        }

        if (! Role::isClient($role)) {
            abort(403, 'Unauthorized access to client area.');
        }

        return $next($request);
    }
}
