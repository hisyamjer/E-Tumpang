<?php

namespace App\Http\Controllers;

use App\Models\Trip;
use Illuminate\Http\Request;
use Inertia\Inertia;

class dashboardController extends Controller
{
    public function index(Request $request)
    {
        $role = (string) $request->session()->get('user_role', 'passenger');

        if ($role === 'driver') {
            $trips = Trip::query()
                ->where('studentID', auth()->id())
                ->withCount('bookings')
                ->orderByDesc('created_at')
                ->limit(6)
                ->get()
                ->filter(function ($trip) {
                    // hide if fully booked
                    if ($trip->bookings_count >= $trip->available_seats) {
                        return false;
                    }

                    // Hide the trip if within 1 hour of departure
                    $departure = $trip->departure_at instanceof \Carbon\CarbonInterface
                        ? $trip->departure_at->copy()->timezone(config('app.timezone'))
                        : \Carbon\Carbon::parse((string) $trip->departure_at, config('app.timezone'));

                    return now(config('app.timezone'))->addHour()->lessThan($departure);
                })
                ->values();

            $trips->each(fn (Trip $trip) => $trip->setAppends([]));

            return Inertia::render('DBdriver/index', [
                'trips' => $trips,
            ]);
        }

        $trips = Trip::query()
            ->with(['student'])
            ->withCount('bookings')
            ->where('status', 'available')
            ->where('studentID', '!=', auth()->id())
            ->orderBy('departure_at')
            ->get()
            ->filter(function ($trip) {
                $departure = $trip->departure_at instanceof \Carbon\CarbonInterface
                    ? $trip->departure_at->copy()->timezone(config('app.timezone'))
                    : \Carbon\Carbon::parse((string) $trip->departure_at, config('app.timezone'));

                // Only show if current time + 1 hour is still BEFORE departure
                return now(config('app.timezone'))->addHour()->lessThan($departure);
            })
            ->values();

        $trips->each(fn (Trip $trip) => $trip->setAppends([]));

        return Inertia::render('DBpassenger/index', [
            'trips' => $trips,
        ]);
    }
}
