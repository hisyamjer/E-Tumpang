<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use App\Models\Trip;
use Carbon\Carbon;
use Illuminate\Http\Request;

class destinationController extends Controller
{
    public function index()
    {
        $now = now(config('app.timezone'));
        $trips = Trip::query()
            ->where('studentID', auth()->id())
            ->where('status', 'available')
            ->withCount('bookings')
            ->with(['bookings.student'])
            ->orderByDesc('departure_time')
            ->get()
            ->map(function ($trip) use ($now) {
                // combine date and time using carbon
                // DB column `departure_time` is `time`, which typically returns `H:i:s`
                $departureTime = trim((string) $trip->departure_time);
                if (preg_match('/^\d{2}:\d{2}$/', $departureTime)) {
                    $departureTime .= ':00';
                }

                $tripDate = $trip->date instanceof \Carbon\CarbonInterface
                    ? $trip->date->format('Y-m-d')
                    : trim((string) $trip->date);
                $departure = Carbon::createFromFormat(
                    'Y-m-d H:i:s',
                    $tripDate . ' ' . $departureTime,
                    config('app.timezone')
                );
                //for check the trip is less than 1 hour from real time
                $trip->is_available = $now->copy()->addHour()->lessThanOrEqualTo($departure);

                return $trip;
            });

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
            'status' => 'in:available,booked,completed',
            'price' => 'required|numeric|min:0',
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'date' => 'required|date_format:Y-m-d',
            'description' => 'nullable|string|max:500',
            'gender_pref' => 'required|string',
            'car_model' => 'required|string',
            'plate_number' => 'required|string',
        ]);

        $departure = Carbon::createFromFormat(
            'Y-m-d H:i',
            $validated['date'] . ' ' . $validated['departure_time'],
            config('app.timezone')
        );

        if(now(config('app.timezone'))->addHour()->greaterThan($departure)){
            return back()->withErrors([ 
                'departure_time' => 'Departure must be at least 1 hour from now.'
            ])->withInput();    
        }

        $validated['studentID'] = auth()->id();
        $validated['created_at'] = now();

         Trip::create($validated);

        return redirect()->route('destination')->with('message', 'Destination created successfully.');
    }

    public function update ( Request $request, Trip $trip) {
        $validated = $request->validate([
            'destination' => 'required|string|max:255',
            'departure_time' => 'required|date_format:H:i',
            'available_seats' => 'required|integer|min:1',
            'status' => 'in:available,booked,completed',
            'price' => 'required|numeric|min:0',
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'date' => 'required|date_format:Y-m-d',
            'description' => 'nullable|string|max:500',
            'gender_pref' => 'required|string',
            'car_model' => 'required|string',
            'plate_number' => 'required|string',
            ]);

            $validated['studentID'] = auth()->id();
            $validated['status'] = 'available';

            $trip->update($validated);
            return redirect()->route('destination')->
            
            with('message', 'Trip was updated');
    }

    public function destroy ( Trip $trip) {

        if ($trip->studentID !== auth()->id()) {
        return redirect()->back()->with('error', 'Unauthorized');
    }
        $trip->delete();

        return redirect()->route('destination')->with('message', 'Trip was cancel');
    }

    public function arrive ($id) {
        $trip = Trip::findOrFail($id);

        // Only the driver can mark it as arrived
        if ((int) $trip->studentID !== (int) auth()->id()) {
            return redirect()->back()->with('error', 'Unauthorized');
        }

        // Must be past departure time
        $departureTime = trim((string) $trip->departure_time);
                if (preg_match('/^\d{2}:\d{2}$/', $departureTime)) {
                    $departureTime .= ':00';
                }

                $tripDate = $trip->date instanceof \Carbon\CarbonInterface
                    ? $trip->date->format('Y-m-d')
                    : trim((string) $trip->date);
                $departure = Carbon::createFromFormat(
                    'Y-m-d H:i:s',
                    $tripDate . ' ' . $departureTime,
                    config('app.timezone')
        );

        if((now(config('app.timezone'))->lessThanOrEqualTo($departure))){    
            return back()->with('error','You cannot complete a trip before departure time');
        }

        $trip->update([
            'status' => 'completed',
        ]);

        return back()->with('message', 'Trip marked as arrived');
    }

    public function history(){

        $now = now(config('app.timezone'));
        
        $history = Trip::query()
            ->where('studentID', auth()->id())
            ->where('status', 'completed')
            ->withCount('bookings')
            ->orderByDesc('date')
            ->orderByDesc('departure_time')
            ->paginate(10);

        return Inertia::render('History/index', [
            'history' => $history,
        ]);

    }
    
}
