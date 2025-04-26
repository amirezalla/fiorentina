<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use App\Models\Calendario;
use Illuminate\Support\Facades\Artisan;


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
                $liveMatches = Calendario::where('status','LIVE')->pluck('match_id');
                foreach ($liveMatches as $matchId) {
                    Artisan::queue('commentary:sync', ['matchId' => $matchId]);
                }
            })->everyMinute();
            
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
