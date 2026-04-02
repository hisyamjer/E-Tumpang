<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
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

    // In this app, the authenticated model is `Student`, not `User`.
    $student = $request->user();

    // Ensure the `user` row exists for this student, then update car details.
    $user = User::firstOrCreate(
        ['studentID' => $student->studentID],
        [
            'role' => (string) session('user_role', 'passenger'),
            // `model` is currently NOT NULL in your DB, so we must provide a value on insert.
            // A later migration can make it nullable if you prefer.
            'model' => '',
        ]
    );

    $updated = $user->update([
        'model' => $validated['model'],
        'plate_number' => $validated['plate_number'],
    ]);

    if ($updated) {
        return redirect()->back()->with('message', 'Car details updated successfully!');
    }

    return redirect()->back()->with('error', 'Failed to update database.');
}
}
