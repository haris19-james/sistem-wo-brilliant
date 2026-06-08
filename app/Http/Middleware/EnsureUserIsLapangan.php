<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsLapangan
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! $request->user()) {
            return redirect()->route('login');
        }

        if ($request->user()->role !== 'lapangan') {
            abort(403, 'Unauthorized access to lapangan panel.');
        }

        return $next($request);
    }
}
