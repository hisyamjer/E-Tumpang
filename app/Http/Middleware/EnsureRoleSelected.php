<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class EnsureRoleSelected
{
    public function handle(Request $request, Closure $next)
    {
        // Admins do not participate in student role selection (driver/passenger).
        if ($request->user('admin')) {
            return $next($request);
        }

        if (!session()->has('user_role')) {
        return redirect()->route('role.index');
    }

        return $next($request);
    }

    public function store (Request $request){
        
        $role = $request->input('role');
        // Save to session so Laravel remembers their "current mode"
        session(['user_role' => $role]);
        return redirect()->route($role.'dashboard');
    }

}
