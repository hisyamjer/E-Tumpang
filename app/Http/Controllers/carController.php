<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class CarController extends Controller
{
    public function index()
    {
        return Inertia::render('Car/index');
    }

    public function store(Request $request)
{
    $validated = $request->validate([
        'model' => 'required|string|max:255',
        'plate_number' => 'required|string|max:255',
    ]);

    $user = Auth::user();
    
    // Explicitly update the specific userID to be safe
    $updated = User::where('userID', $user->userID)->update([
        'model' => $validated['model'],
        'plate_number' => $validated['plate_number'],
    ]);

    if ($updated) {
        return redirect()->back()->with('message', 'Car details updated successfully!');
    }

    return redirect()->back()->with('error', 'Failed to update database.');
}
}