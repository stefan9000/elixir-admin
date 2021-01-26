<?php

namespace App\Http\Middleware;

use Closure;

class IsRegular
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (!auth()->user()->isDoorman() && !auth()->user()->isAdmin()) {
            return $next($request);
        } else {
            return abort(403);
        }
    }
}
