<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EnsureStudentNotBlocked
{
    public function handle(Request $request, Closure $next)
    {
        $student = $request->user('web');

        if ($student && $student->is_blocked) {
            Auth::guard('web')->logout();
            $request->session()->forget('user_role');
            $request->session()->regenerateToken();
            $request->session()->regenerate();

            return redirect('/login')->with('error', 'Your account has been blocked.');
        }

        return $next($request);
    }
}

