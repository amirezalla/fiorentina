<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\Calendario;
use App\Models\MatchLineups;
use Illuminate\Support\Arr;

class SyncLineup extends Command
{
    /** php artisan lineup:sync {matchId} */
    protected $signature   = 'lineup:sync {matchId}';
    protected $description = 'Pull FlashScore line-ups for one match and upsert them into match_lineups table';

    public function handle(): int
    {
        $matchId = $this->argument('matchId');
        $apiKey  = '1e9b76550emshc710802be81e3fcp1a0226jsn069e6c35a2bb';

        $url = "https://flashlive-sports.p.rapidapi.com/v1/events/lineups"
             . "?locale=it_IT&event_id={$matchId}";

        $resp = Http::withHeaders([
                    'x-rapidapi-host' => 'flashlive-sports.p.rapidapi.com',
                    'x-rapidapi-key'  => $apiKey,
                ])->get($url);

        $blocks = $resp->json('DATA') ?? [];
        if (!$blocks) {
            $this->warn("No lineup data for {$matchId}");
            return Command::SUCCESS;
        }

        /* ───────────────────────────────────────────────────────────
           Filter only the blocks we need
        ─────────────────────────────────────────────────────────── */
        $keep = [
            'Fiorentina Subs',
            'Fiorentina Coach',
            'Fiorentina Initial Lineup',
            'Another Subs',
            'Another Coach',
            'Another Initial Lineup',
        ];

        $wanted = collect($blocks)
                    ->whereIn('FORMATION_NAME', $keep)
                    ->values();

        /* ───────────────────────────────────────────────────────────
           Upsert every PLAYER/COACH inside each block
        ─────────────────────────────────────────────────────────── */
        foreach ($wanted as $block) {
            $formationName = $block['FORMATION_NAME'];
            $formationDispo = $block['FORMATION_DISPOSITION'] ?? null;

            foreach ($block['PLAYERS'] ?? [] as $p) {
                // Map API keys to DB columns
                $attrs = [
                    'match_id'          => $matchId,
                    'player_id'         => $p['PLAYER_ID'] ?? null,
                    'formation_name'    => $formationName,
                    'formation_disposition' => $formationDispo,
                    'player_full_name'  => $p['PLAYER_FULL_NAME'] ?? null,
                    'short_name'        => $p['PLAYER_SHORT_NAME'] ?? null,
                    'player_position'   => $p['PLAYER_POSITION'] ?? null,
                    'player_rating'     => $p['PLAYER_RATING'] ?? null,
                    'player_image'      => Arr::first($p['PLAYER_IMAGES'] ?? []),
                    'is_captain'        => $p['IS_CAPTAIN'] ?? 0,
                ];

                MatchLineups::updateOrCreate(
                    [
                        'match_id'  => $matchId,
                        'player_id' => $attrs['player_id'],
                    ],
                    $attrs
                );
            }
        }

        Log::info("Synced ".count($wanted)." lineup blocks for {$matchId}");
        $this->info("{$matchId}: synced line-ups");

        return Command::SUCCESS;
    }
}
