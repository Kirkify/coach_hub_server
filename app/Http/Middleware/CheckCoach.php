<?php

namespace App\Http\Middleware;

use Illuminate\Support\Facades\Auth;
use Closure;

class CheckCoach
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next, $guard = 'api')
    {
        if (Auth::guard($guard)->check()) {
            $profile = Auth::user()->coachBaseProfile;

            if ($profile) {
                return $next($request);
            }
        }

        abort(403, 'You must create a coach profile before accessing this resource');
    }
}
