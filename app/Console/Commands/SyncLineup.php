<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\Calendario;

class SyncLineup extends Command
{
    /** php artisan lineup:sync {matchId} */
    protected $signature   = 'lineup:sync {matchId}';
    protected $description = 'Pull FlashScore line-ups for one match and store them as JSON in calendario.lineups';

    public function handle(): int
    {
        $matchId = $this->argument('matchId');
        $apiKey  = '1e9b76550emshc710802be81e3fcp1a0226jsn069e6c35a2bb';

        $url   = "https://flashlive-sports.p.rapidapi.com/v1/events/lineups"
               . "?locale=it_IT&event_id={$matchId}";

        $resp  = Http::withHeaders([
                    'x-rapidapi-host' => 'flashlive-sports.p.rapidapi.com',
                    'x-rapidapi-key'  => $apiKey,
                ])->get($url);

        $rows  = $resp->json('DATA') ?? [];
        if (!$rows) {
            $this->warn("No lineup data for {$matchId}");
            return Command::SUCCESS;
        }

        // keep only the three blocks you want
        $wanted = collect($rows)->filter(function ($item) {
            return in_array($item['FORMATION_NAME'], [
                'Fiorentina Subs',
                'Fiorentina Coach',
                'Fiorentina Initial Lineup',
                'Another Subs',
                'Another Coach',
                'Another Initial Lineup',
            ]);
        })->values();

        Calendario::where('match_id', $matchId)
                  ->update(['lineups' => $wanted->toJson()]);

        Log::info("Updated line-ups for {$matchId}");
        $this->info("{$matchId}: ".count($wanted).' blocks');

        return Command::SUCCESS;
    }
}
