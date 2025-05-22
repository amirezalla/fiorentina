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
    protected $description = 'Sync one match’s score + status from FlashScore “team results” endpoint';

    public function handle(): int
    {
        $matchId = $this->argument('matchId');

        // ───── 1. Find the match row so we know the team-ids to query  ─────
        $row = Calendario::where('match_id', $matchId)->first();

        if (!$row) {
            $this->error("Match {$matchId} not found in DB.");
            return Command::FAILURE;
        }

        // You may name these columns differently (edit if needed)
        $teamIds = array_filter([
            $row->home_team_id  ?? null,
            $row->away_team_id  ?? null,
        ]);

        if (empty($teamIds)) {
            $this->error("No team IDs stored for match {$matchId}.");
            return Command::FAILURE;
        }

        // ───── 2. Call the RapidAPI endpoint for each team until we find the event ─────
        $apiKey = config('services.flashscore.key', '1e9b76550emshc710802be81e3fcp1a0226jsn069e6c35a2bb');   // put key in config/.env!
        $host   = 'flashlive-sports.p.rapidapi.com';

        $found  = null;
        foreach ($teamIds as $teamId) {
            $url = "https://{$host}/v1/teams/results"
                 . '?sport_id=1&locale=en_INT&page=1'
                 . "&team_id={$teamId}";

            $resp = Http::withHeaders([
                        'x-rapidapi-host' => $host,
                        'x-rapidapi-key'  => $apiKey,
                    ])->get($url);

            if (!$resp->ok()) {
                Log::warning("FlashScore API error for team {$teamId}: {$resp->status()}");
                continue;
            }

            $results = $resp->json('DATA') ?? [];
            foreach ($results as $block) {
                foreach ($block['EVENTS'] ?? [] as $event) {
                    if (($event['EVENT_ID'] ?? null) === $matchId) {
                        $found = $event;
                        break 2;      // exit both loops
                    }
                }
            }
        }

        if (!$found) {
            $this->warn("Event {$matchId} not returned for either team.");
            return Command::SUCCESS;   // nothing to update yet
        }

        // ───── 3. Build the payload and update Calendario ─────
        $stageType  = $found['STAGE_TYPE'] ?? 'UNKNOWN';
        $homeScore  = $found['HOME_SCORE_CURRENT'] ?? 0;
        $awayScore  = $found['AWAY_SCORE_CURRENT'] ?? 0;
        $minute     = $found['EVENT_MINUTE'] ?? null;

        $payload = [
            'status' => $stageType,        // e.g. LIVE, FINISHED, etc.
            'score'  => json_encode(['home' => $homeScore, 'away' => $awayScore]),
            'minute' => $minute,
        ];

        // Force DB status to FINISHED when the API says so
        if ($stageType === 'FINISHED') {
            $payload['status'] = 'FINISHED';
        }

        $row->update($payload);

        Log::info("Score synced for {$matchId}", $payload);
        $this->info("{$matchId}: {$homeScore}-{$awayScore} ({$payload['status']})");

        return Command::SUCCESS;
    }
}
