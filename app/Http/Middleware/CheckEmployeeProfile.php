<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckEmployeeProfile
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (auth()->check() && !auth()->user()->employee) {
            return redirect()->route('profile.complete')
                ->with('warning', 'Please complete your employee profile.');
        }

        return $next($request);
    }
}