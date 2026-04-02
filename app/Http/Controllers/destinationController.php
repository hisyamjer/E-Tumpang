<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use App\Models\Trip;
use Illuminate\Http\Request;

class destinationController extends Controller
{
    public function index()
    {
        $trips = Trip::query()
            ->where('studentID', auth()->id())
            ->withCount('bookings')
            ->orderByDesc('departure_time')
            ->get();

        return Inertia::render('Destination/index', [
            'trips' => $trips,
        ]);
    }

    public function create()
    {
        return Inertia::render('Destination/create');
    }

    public function store ( Request $request) {

        $validated = $request->validate([
            'destination' => 'required|string|max:255',
            'departure_time' => 'required|date_format:H:i',
            'available_seats' => 'required|integer|min:1',
            'status' => 'in:available,booked',
            'price' => 'required|numeric|min:0',
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'date' => 'required|date_format:Y-m-d',
        ]);

        $validated['studentID'] = auth()->id();
        $validated['created_at'] = now();

         Trip::create($validated);

        return redirect()->route('destination')->with('success', 'Destination created successfully.');
    }

    public function update ( Request $request, Trip $trip) {
        $validated = $request->validate([
            'destination' => 'required|string|max:255',
            'departure_time' => 'required|date_format:H:i',
            'available_seats' => 'required|integer|min:1',
            'status' => 'in:available,booked',
            'price' => 'required|numeric|min:0',
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'date' => 'required|date_format:Y-m-d',
            ]);

            $trip->update($validated);
            return redirect()->route('destination')->
            
            with('success', 'Trip was updated');
    }

    public function destroy ( Trip $trip) {

        if ($trip->studentID !== auth()->id()) {
        return redirect()->back()->with('error', 'Unauthorized');
    }
        $trip->delete();

        return redirect()->route('destination')->with('success', 'Trip was cancel');
    }
}
