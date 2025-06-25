<?php

namespace App\Http\Middleware;

use App\Traits\ApiResponse;
use Closure;

class VerifiedMobile
{
    use ApiResponse;

    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if ($request->user()->mobile_verified_at == null) {

            return $this->genericError('Please verify your mobile number.');
        }
        if ($request->user()->is_active == 0) {

            return $this->genericError('Your account is disabled. Please contact to our support for help.');
        }


        return $next($request);
    }
}
