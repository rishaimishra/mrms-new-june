<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckPermission
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string  $permission
     * @return mixed
     */
    public function handle(Request $request, Closure $next, $permission)
    {
        $user = Auth::user();

        // Check if the user has the required permission
        // if (!$user || !$user->hasPermissionTo($permission)) {
        //     // Optionally, you can redirect or throw an error
        //     return redirect()->route('home')->with('error', 'Access Denied.');
        // }

        return $next($request);
    }
}
