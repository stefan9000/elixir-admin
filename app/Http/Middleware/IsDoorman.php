<?php

namespace App\Http\Middleware;

use Closure;

class IsDoorman
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
        if (auth()->user()->isDoorman()) {
            return $next($request);
        } else {
            return abort(403);
        }
    }
}
