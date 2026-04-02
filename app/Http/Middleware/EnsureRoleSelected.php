<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class EnsureRoleSelected
{
    public function handle(Request $request, Closure $next)
    {
        if (!$request->user()) {
            return $next($request);
        }

        if (!$request->session()->has('user_role')) {
            return redirect()->guest(route('role.index'));
        }

        return $next($request);
    }
}

