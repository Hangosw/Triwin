<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\HanetCheckinService;
use Carbon\Carbon;

class SyncHanetCheckins extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'hanet:sync-checkin {--date= : The date to sync checkins (yyyy-mm-dd)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync checkin data from Hanet AI camera for a specific date';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(HanetCheckinService $hanetService)
    {
        $date = $this->option('date') ?? Carbon::today()->toDateString();

        $this->info("Starting Hanet sync for date: {$date}...");

        $result = $hanetService->syncCheckinsToAttendance($date);

        if ($result['success']) {
            $this->success("Successfully synced {$result['count']} employee checkins.");
            return Command::SUCCESS;
        } else {
            $this->error("Failed to sync: " . $result['error']);
            return Command::FAILURE;
        }
    }
}
