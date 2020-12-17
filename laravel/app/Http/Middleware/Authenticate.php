<?php

namespace App\Http\Middleware;

use Closure;
use Auth;
class Authenticate
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
        if (! Auth::check()) {
           return redirect(env('user').'/login')->with('error','Please login for access this page');
       }
        
        return $next($request);
    }
}
