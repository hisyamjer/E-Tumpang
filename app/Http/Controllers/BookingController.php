<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Trip;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class BookingController extends Controller
{
    public function index()
    {
        $myBookings = Booking::where('studentID', Auth::id())
            ->with([
                'trip' => fn ($query) => $query->withCount('bookings'),
            ])
            ->where('status', 'pending')
            ->orderByDesc('booking_date')
            ->get();

        return Inertia::render('Booking/index', [
            'bookings' => $myBookings
        ]);
    }

    public function join(Request $request, $tripId)
    {
        $trip = Trip::findOrFail($tripId);
        $userId = auth()->id();

        // 1. RULE: Driver cannot book their own trip
        if ((int) $trip->studentID === (int) $userId) {
            return back()->with('error', 'Ops! You are the driver for this trip.');
        }

        // 2. RULE: Must book at least 1 hour before departure
        // Combine date and time into a single Carbon object
        $departureTime = trim((string) $trip->departure_time);
        if (preg_match('/^\d{2}:\d{2}$/', $departureTime)) {
            $departureTime .= ':00';
        }
        $tripDate = $trip->date instanceof \Carbon\CarbonInterface
            ? $trip->date->format('Y-m-d')
            : trim((string) $trip->date);
        $departureDatetime = Carbon::createFromFormat(
            'Y-m-d H:i:s',
            $tripDate . ' ' . $departureTime,
            config('app.timezone')
        );
          
        if (now(config('app.timezone'))->addHour()->greaterThan($departureDatetime)) {
            return back()->with('error', 'Booking closed! You must book at least 1 hour before departure.');
        }

        // 3. Get the current number of passengers who already joined
        $currentBookingsCount = $trip->bookings()->count();

        // 4. Check if there is space
        if ($currentBookingsCount >= $trip->available_seats) {
            return back()->with('error', 'Sorry, this car is already full!');
        }

        // 5. Prevent the same student from booking twice
        $alreadyJoined = Booking::where('tripID', $tripId)
                                ->where('studentID', $userId)
                                ->exists();
        if ($alreadyJoined) {
            return back()->with('error', 'You are already in this trip!');
        }

        // 6. Create the booking
        Booking::create([
            'tripID' => $tripId,
            'studentID' => $userId,
            'seats_booked' => 1,
            'status' => 'confirmed',
            'booking_date' => now(config('app.timezone')),
        ]);

        return redirect()->route('booking')->with('message', 'Seat booked successfully!');
    }

    public function destroy($id)
    {
        // 1. Find the booking by its primary key (bookingID)
        $booking = Booking::findOrFail($id);

        // 2. Safety Check: Ensure the person trying to cancel is the owner of the booking
        if ((int) $booking->studentID !== (int) auth()->id()) {
            return back()->with('error', 'Unauthorized action.');
        }

        // 3. Optional: Add a "1-hour before" rule for cancellation too
        // This prevents passengers from canceling last minute
        $departureTime = trim((string) $booking->trip->departure_time);
        if (preg_match('/^\d{2}:\d{2}$/', $departureTime)) {
            $departureTime .= ':00';
        }
        $tripDate = $booking->trip->date instanceof \Carbon\CarbonInterface
            ? $booking->trip->date->format('Y-m-d')
            : trim((string) $booking->trip->date);
        $departure = Carbon::createFromFormat(
            'Y-m-d H:i:s',
            $tripDate . ' ' . $departureTime,
            config('app.timezone')
        );
        if (now(config('app.timezone'))->addHour()->greaterThan($departure)) {
            return back()->with('error', 'Cannot cancel within 1 hour of departure.');
        }

        // 4. Delete the record
        $booking->delete();

        return redirect()->route('dashboard')->with('message', 'Booking cancelled successfully.');
    }
}
