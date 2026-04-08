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
                    $departureTime = trim((string) $trip->departure_time);
                    if (preg_match('/^\d{2}:\d{2}$/', $departureTime)) {
                        $departureTime .= ':00';
                    }

                    $tripDate = $trip->date instanceof \Carbon\CarbonInterface
                        ? $trip->date->format('Y-m-d')
                        : trim((string) $trip->date);
                    $departure = \Carbon\Carbon::createFromFormat(
                        'Y-m-d H:i:s',
                        $tripDate . ' ' . $departureTime,
                        config('app.timezone')
                    );

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
            ->orderBy('date')
            ->orderBy('departure_time')
            ->get()
            ->filter(function ($trip) {
                $departureTime = trim((string) $trip->departure_time);
                if (preg_match('/^\d{2}:\d{2}$/', $departureTime)) {
                    $departureTime .= ':00';
                }

                $tripDate = $trip->date instanceof \Carbon\CarbonInterface
                    ? $trip->date->format('Y-m-d')
                    : trim((string) $trip->date);
                $departure = \Carbon\Carbon::createFromFormat(
                    'Y-m-d H:i:s',
                    $tripDate . ' ' . $departureTime,
                    config('app.timezone')
                );

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
