<?php

namespace App\Console\Commands;

use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use App\Models\Trip;

#[Signature('app:cleanup-trip')]
#[Description('Delete trips that are older than a certain date')]
class CleanupTrip extends Command
{
    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Cleaning up old trips...');

        // Define the cutoff date (e.g., delete trips older than 30 days)
        $cutoffDate = now()->subDays(30);

        // Delete trips that are older than the cutoff date
        $deletedCount = Trip::where('created_at', '<', $cutoffDate)->delete();

        $this->info("Deleted {$deletedCount} old trips.");
    }
}
