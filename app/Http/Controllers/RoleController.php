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

        // Persist role to `user` table so it isn't lost on session reset.
        $student = $request->user();
        User::updateOrCreate(
            ['studentID' => $student->studentID],
            [
                'role' => $request->role,
                // Prevent MySQL error 1364 if `model` is NOT NULL without a default.
                'model' => '',
            ]
        );

        return redirect()->intended(route('dashboard'));
    }
}
