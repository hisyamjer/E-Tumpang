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

        // Persist role to `user` table only if it has not been set yet.
        // This allows users to switch roles per-session (e.g. act as passenger)
        // while keeping their primary role (e.g. driver) unchanged in the DB.
        $student = $request->user();
        $user = User::where('studentID', $student->studentID)->first();

        if (!$user) {
            User::create([
                'studentID' => $student->studentID,
                'role' => $request->role,
                // Prevent MySQL error 1364 if `model` is NOT NULL without a default.
                'model' => '',
            ]);
        } elseif (!$user->role) {
            $user->update([
                'role' => $request->role,
            ]);
        }

        return redirect()->intended(route('dashboard'));
    }
}
