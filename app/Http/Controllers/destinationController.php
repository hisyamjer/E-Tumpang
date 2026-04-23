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
            ->orderByDesc('departure_at')
            ->get()
            ->map(function ($trip) use ($now) {
                $departure = $trip->departure_at instanceof \Carbon\CarbonInterface
                    ? $trip->departure_at->copy()->timezone(config('app.timezone'))
                    : Carbon::parse((string) $trip->departure_at, config('app.timezone'));
                //for check the trip is less than 1 hour from real time
                $trip->is_available = $now->copy()->addHour()->lessThanOrEqualTo($departure);

                return $trip;
            });

        return Inertia::render('Destination/index', [
            'trips' => $trips,
            'hasActiveTrip' => Trip::where('studentID', auth()->id())->whereIn('status', ['available', 'on-going'])->exists(),
        ]);
    }

    public function create()
    {
        return Inertia::render('Destination/create');
    }

    public function store ( Request $request) {
 
        $validated = $request->validate([
            'destination' => 'required|string|max:255',
            'departure_at' => 'required_without_all:date,departure_time|date',
            'departure_time' => 'required_without:departure_at|date_format:H:i',
            'available_seats' => 'required|integer|min:1',
            'status' => 'in:available,booked,completed',
            'price' => 'required|numeric|min:0',
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'date' => 'required_without:departure_at|date_format:Y-m-d',
            'description' => 'nullable|string|max:500',
            'gender_pref' => 'required|string',
            'car_model' => 'required|string',
            'plate_number' => 'required|string',
        ]);

        $departure = array_key_exists('departure_at', $validated) && $validated['departure_at']
            ? Carbon::parse($validated['departure_at'], config('app.timezone'))
            : Carbon::createFromFormat(
                'Y-m-d H:i',
                $validated['date'] . ' ' . $validated['departure_time'],
                config('app.timezone')
            );

        if(now(config('app.timezone'))->addHour()->greaterThan($departure)){
            $errorField = ($request->filled('departure_at') || (array_key_exists('departure_at', $validated) && $validated['departure_at']))
                ? 'departure_at'
                : 'departure_time';
            return back()->withErrors([ 
                $errorField => 'Departure must be at least 1 hour from now.'
            ])->withInput();    
        }

        $validated['studentID'] = auth()->id();
        $validated['created_at'] = now();
        $validated['departure_at'] = $departure;
        unset($validated['date'], $validated['departure_time']);

         Trip::create($validated);

        return redirect()->route('destination')->with('message', 'Destination created successfully.');
    }

    public function update ( Request $request, Trip $trip) {
        $validated = $request->validate([
            'destination' => 'required|string|max:255',
            'departure_at' => 'required_without_all:date,departure_time|date',
            'departure_time' => 'required_without:departure_at|date_format:H:i',
            'available_seats' => 'required|integer|min:1',
            'status' => 'in:available,booked,completed',
            'price' => 'required|numeric|min:0',
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'date' => 'required_without:departure_at|date_format:Y-m-d',
            'description' => 'nullable|string|max:500',
            'gender_pref' => 'required|string',
            'car_model' => 'required|string',
            'plate_number' => 'required|string',
            ]);

            $departure = array_key_exists('departure_at', $validated) && $validated['departure_at']
                ? Carbon::parse($validated['departure_at'], config('app.timezone'))
                : Carbon::createFromFormat(
                    'Y-m-d H:i',
                    $validated['date'] . ' ' . $validated['departure_time'],
                    config('app.timezone')
                );

            $validated['studentID'] = auth()->id();
            $validated['status'] = 'available';
            $validated['departure_at'] = $departure;
            unset($validated['date'], $validated['departure_time']);

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
        $departure = $trip->departure_at instanceof \Carbon\CarbonInterface
            ? $trip->departure_at->copy()->timezone(config('app.timezone'))
            : Carbon::parse((string) $trip->departure_at, config('app.timezone'));

        if((now(config('app.timezone'))->lessThanOrEqualTo($departure))){    
            return back()->with('error','You cannot complete a trip before departure time');
        }

        $trip->update([
            'status' => 'completed',
        ]);

        return back()->with('message', 'Trip marked as arrived');
    }

    public function history()
    {
        $user = auth()->user();
        $studentIdentifier = $user->studentID;
        
        // Check the SESSION role, not the DB role
        $sessionRole = session('user_role');

        if ($sessionRole === 'driver') {
            $history = Trip::query()
                ->where('studentID', $studentIdentifier)
                ->where('status', 'completed')
                ->withCount('bookings')
                ->orderByDesc('departure_at')
                ->paginate(10);

            return Inertia::render('History/index', [
                'history' => $history,
            ]);
        } 

        // If session is 'passenger'
        $history = Trip::query()
            ->whereHas('bookings', function ($query) use ($studentIdentifier) {
                $query->where('studentID', $studentIdentifier); 
            })
            ->where('status', 'completed')
            ->withCount('bookings')
            ->orderByDesc('departure_at')
            ->paginate(10);

        return Inertia::render('History/passenger', [
            'history' => $history,
        ]);
    }
}
