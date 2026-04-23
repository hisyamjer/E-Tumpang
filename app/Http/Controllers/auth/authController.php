<?php

namespace App\Http\Controllers\auth;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\Admin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;

class authController extends Controller
{
    public function store(Request $request){
    $credentials = $request->validate([
        'studentID' => 'required', // Removed 'integer' in case ID has leading zeros or symbols
        'password' => 'required',
    ]);

    $admin = Admin::where('email', $credentials['studentID'])->first();
         
    // Passwords should be stored hashed (bcrypt/argon) for production.
    if ($admin) {
        $plain = (string) $credentials['password'];
        $stored = (string) $admin->password;

        $isValid = Hash::check($plain, $stored) || hash_equals($stored, $plain);
        if ($isValid && !Hash::check($plain, $stored)) {
            $admin->forceFill(['password' => Hash::make($plain)])->save();
        }

        if ($isValid) {
        Auth::guard('admin')->login($admin);
        $request->session()->forget('user_role');
        $request->session()->regenerate();
        return redirect()->route('admin.dashboard');
        }
    }

    $user = Student::query()->where('studentID', $credentials['studentID'])->first();

    if ($user && $user->is_blocked) {
        throw ValidationException::withMessages([
            'studentID' => ['Your account has been blocked. Please contact admin.'],
        ]);
    }

    if ($user) {
        $plain = (string) $credentials['password'];
        $stored = (string) $user->password;

        $isValid = Hash::check($plain, $stored) || hash_equals($stored, $plain);
        if ($isValid && !Hash::check($plain, $stored)) {
            $user->forceFill(['password' => Hash::make($plain)])->save();
        }

        if ($isValid) {
        Auth::guard('web')->login($user);
        $request->session()->regenerate();

        // Let the middleware force role selection first.
        return redirect()->route('role.index');
        }
    }

    if (false && Auth::attempt($credentials)) {
        $request->session()->regenerate();
        
        // ✅ ALWAYS redirect after a successful POST request
        return redirect()->intended('/dashboard');
    }

    throw ValidationException::withMessages([
        'studentID' => ['The provided credentials do not match our records.'],
        'password' => ['Please check your password and try again.']
    ]);
    }

    public function login()
    {
        return inertia::render('Auth/login');
    }

public function logout(Request $request)
{
    if (Auth::guard('admin')->check()) {
        Auth::guard('admin')->logout();
    }

    if (Auth::guard('web')->check()) {
        Auth::guard('web')->logout();
    }

    $request->session()->forget('user_role');
    $request->session()->invalidate();
    $request->session()->regenerateToken();

    return redirect('/login');
}
}
