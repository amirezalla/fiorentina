<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Calendario;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;


class StartScheduledMatches extends Command
{
    protected $signature = 'matches:start-scheduled';
    protected $description = 'Check for matches starting soon and set them LIVE';

    public function handle(): int
    {
        
        // Log the command execution
        Log::info('StartScheduledMatches command executed.');
        $now = Carbon::now();
        $minutesLater = $now->copy()->addMinutes(15);

        $matches = Calendario::where('status', 'SCHEDULED')
                    ->whereBetween('match_date', [$now, $minutesLater])
                    ->get();

        if ($matches->isEmpty()) {
            $this->info('No matches to start.');
            return Command::SUCCESS;
        }

        foreach ($matches as $match) {
            $match->update([
                'status' => 'LIVE'
            ]);
            $this->info("Match {$match->match_id} set to LIVE.");
        }

        return Command::SUCCESS;
    }
}
