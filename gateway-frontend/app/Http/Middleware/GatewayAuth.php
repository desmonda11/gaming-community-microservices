<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class GatewayAuth
{
    public function handle(Request $request, Closure $next)
    {
        if (!session()->has('token')) {
            return redirect('/login');
        }

        return $next($request);
    }
}
