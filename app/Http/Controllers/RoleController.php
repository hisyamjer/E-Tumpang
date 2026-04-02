<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\User;

class RoleController extends Controller
{
    public function index()
    {
        if (session()->has('user_role')) {
            return redirect()->route('dashboard');
        }

        return Inertia::render('RoleSelection');
    }

    public function store(Request $request)
    {
        $request->validate([
            'role' => 'required|in:driver,passenger'
        ]);

        // Save selection to the session
        session(['user_role' => $request->role]);

        return redirect()->intended(route('dashboard'));
    }
}
