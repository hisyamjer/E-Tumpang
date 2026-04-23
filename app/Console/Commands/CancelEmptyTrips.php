<?php

namespace App\Console\Commands;

use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use App\Models\Trip;

#[Signature('app:cancel-empty-trips')]
#[Description('Command description')]
class CancelEmptyTrips extends Command
{
    /**
     * Execute the console command.
     */
   public function handle()
    {
        $now = now(config('app.timezone'));
        $count = 0;

        // Get all trips that are available
        $trips = Trip::where('status', 'available')
            ->withCount('bookings')
            ->get();

        foreach ($trips as $trip) {
            $departure = $trip->departure_at instanceof \Carbon\CarbonInterface
                ? $trip->departure_at->copy()->timezone(config('app.timezone'))
                : \Carbon\Carbon::parse((string) $trip->departure_at, config('app.timezone'));

            // Kalau tinggal kurang 60 minit DAN tak ada orang book
            if ($now->diffInMinutes($departure, false) <= 60 && $trip->bookings_count === 0) {
                $trip->status = 'cancelled';
                $trip->save();
                $count++;
            }
        }

        $this->info("{$count} empty trips have been cancelled successfully.");
    }
}
