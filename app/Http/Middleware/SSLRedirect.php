<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\App;

class SSLRedirect
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
        if (!$request->secure() && App::environment() === 'production') {
            return redirect()->secure($request->getRequestUri());
        }
        return $next($request);
    }
}
