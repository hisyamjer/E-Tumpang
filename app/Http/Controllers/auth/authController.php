<?php

namespace App\Http\Controllers\auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;

class authController extends Controller
{
    public function store(Request $request)
    {
        $credentials = $request->validate([
            'studentID' => 'required|integer',
            'password' => 'required',
        ]);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            return inertia::render('dashboard/index');
        }

        throw ValidationException::withMessages([
            'studentID' => ['The provided credentials do not match our records.'],
        ]);
    }
    public function login()
    {
        return inertia::render('Auth/login');
    }
}
