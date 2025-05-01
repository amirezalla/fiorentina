<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use App\Models\Calendario;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;


class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        $schedule->command('queue:work --timeout=60 --tries=1 --once')
            ->everyMinute()
            ->withoutOverlapping();


            $schedule->call(function () {
                Log::info('Running commentary sync task.');
                $liveMatches = Calendario::where('status','LIVE')->pluck('match_id');
                foreach ($liveMatches as $matchId) {
                    Artisan::queue('commentary:sync', ['matchId' => $matchId]);
                }
            })->everyMinute();


                // Sync every live match in one go, every two minutes
    $schedule->call(function () {
        Log::debug('Running score sync task');
        $liveIds = Calendario::where('status', 'LIVE')->pluck('match_id');

        foreach ($liveIds as $id) {
            Artisan::queue('score:sync', ['matchId' => $id]);
        }
    })->everyTwoMinutes();

    $schedule->call(function () {
        $ids = Calendario::whereIn('status', ['NS','LIVE'])   // only before / during match
                         ->pluck('match_id');
        foreach ($ids as $id) {
            Artisan::queue('lineup:sync', ['matchId' => $id]);
        }
    })->everyMinute();
    



                /*
    |──────────────────────────────────────────────────────────
    | 2. SUMMARY / RIASSUNTO → every 1 min 30 sec
    |    (run each minute but dispatch the job with +30 s delay)
    |──────────────────────────────────────────────────────────
    */
    $schedule->call(function () {
        Log::info('⏱  summary sync (+30 s delay)');
        $liveMatches = Calendario::where('status', 'LIVE')->pluck('match_id');

        foreach ($liveMatches as $matchId) {
            Artisan::queue('summary:sync', ['matchId' => $matchId])
                   ->delay(now()->addSeconds(30));   // 90-second cadence
        }
    })->everyMinute();


    /*
    |──────────────────────────────────────────────────────────
    | 3. STATISTICS → every 10 minutes
    |──────────────────────────────────────────────────────────
    */
    $schedule->call(function () {
        Log::info('⏱  stats sync');
        $liveMatches = Calendario::where('status', 'LIVE')->pluck('match_id');

        foreach ($liveMatches as $matchId) {
            Artisan::queue('stats:sync', ['matchId' => $matchId]);
        }
    })->everyTenMinutes();    
            
            $schedule->command('matches:start-scheduled')->everyTwoMinutes();

    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
