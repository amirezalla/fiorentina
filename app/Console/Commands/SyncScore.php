<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\Calendario;

class SyncScore extends Command
{
    /** php artisan score:sync {matchId} */
    protected $signature   = 'score:sync {matchId}';
    protected $description = 'Pull FlashScore for a single match and update only the score-related columns';

    public function handle(): int
    {
        $matchId = $this->argument('matchId');
        $apiKey  = '1e9b76550emshc710802be81e3fcp1a0226jsn069e6c35a2bb';

        // A “detail” call gives us the current score, status, minute, etc.
        $url = "https://flashlive-sports.p.rapidapi.com/v1/events/detail"
             . "?locale=it_IT&event_id={$matchId}";

        $resp = Http::withHeaders([
                    'x-rapidapi-host' => 'flashlive-sports.p.rapidapi.com',
                    'x-rapidapi-key'  => $apiKey,
                ])->get($url);

        $row = $resp->json('DATA.0');         // single match → first element
        if (!$row) {
            $this->warn("No data for {$matchId}");
            return Command::SUCCESS;
        }

        $payload = [
            'status' => $row['STAGE_TYPE'],
            'score'  => json_encode([
                'home' => $row['HOME_SCORE_CURRENT'] ?? 0,
                'away' => $row['AWAY_SCORE_CURRENT'] ?? 0,
            ]),
            'minute' => $row['EVENT_MINUTE'] ?? null,   // optional but handy
        ];

        Calendario::where('match_id', $matchId)->update($payload);

        Log::info("Updated score for {$matchId}", $payload);
        $this->info("{$matchId}: {$payload['score']} ({$payload['status']})");

        return Command::SUCCESS;
    }
}
