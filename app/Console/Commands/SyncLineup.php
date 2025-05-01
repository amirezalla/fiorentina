<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Arr;
use App\Models\MatchLineups;

class SyncLineup extends Command
{
    /** php artisan lineup:sync {matchId} */
    protected $signature   = 'lineup:sync {matchId}';
    protected $description = 'Fetch FlashScore line-ups for one match and upsert them into match_lineups';

    /* --------------------------------------------------------------------
       Configuration
    -------------------------------------------------------------------- */
    private string $apiKey = '1e9b76550emshc710802be81e3fcp1a0226jsn069e6c35a2bb';

    private array $keepBlocks = [
        'Fiorentina Subs',
        'Fiorentina Coach',
        'Fiorentina Initial Lineup',
        'Another Subs',
        'Another Coach',
        'Another Initial Lineup',
    ];

    public function handle(): int
    {
        $matchId = $this->argument('matchId');

        /* --------------------------------------------------------------
           1. Call FlashScore
        -------------------------------------------------------------- */
        $url = "https://flashlive-sports.p.rapidapi.com/v1/events/lineups"
             . "?locale=it_IT&event_id={$matchId}";

        $resp = Http::withHeaders([
                    'x-rapidapi-host' => 'flashlive-sports.p.rapidapi.com',
                    'x-rapidapi-key'  => $this->apiKey,
                ])->timeout(15)->get($url);

        if (!$resp->ok()) {
            Log::warning("lineup:sync {$matchId} – HTTP ".$resp->status(), [
                'body' => $resp->body(),
            ]);
            $this->error("API error ".$resp->status());
            return Command::FAILURE;
        }

        $data = $resp->json('DATA');
        if (!is_array($data)) {
            Log::warning("lineup:sync {$matchId} – malformed DATA", [
                'payload' => $resp->json(),
            ]);
            $this->error('API returned unexpected schema (no DATA array)');
            return Command::FAILURE;
        }

        /* --------------------------------------------------------------
           2. Keep only wanted blocks
        -------------------------------------------------------------- */
        $blocks = collect($data)->whereIn('FORMATION_NAME', $this->keepBlocks);

        if ($blocks->isEmpty()) {
            Log::info("lineup:sync {$matchId} – no wanted blocks present");
            $this->warn('No matching formation blocks yet (maybe line-ups not published)');
            return Command::SUCCESS;
        }

        Log::debug("lineup:sync {$matchId} – received ".$blocks->count().' blocks', [
            'blocks' => $blocks->pluck('FORMATION_NAME'),
        ]);

        /* --------------------------------------------------------------
           3. Upsert players
        -------------------------------------------------------------- */
        $inserted = 0;
        $updated  = 0;

        foreach ($blocks as $block) {
            $formationName  = $block['FORMATION_NAME'];
            $formationDispo = $block['FORMATION_DISPOSITION'] ?? null;

            foreach (($block['PLAYERS'] ?? []) as $p) {
                $key = [
                    'match_id'  => $matchId,
                    'player_id' => $p['PLAYER_ID'] ?? null,
                ];

                $attrs = [
                    'formation_name'        => $formationName,
                    'formation_disposition' => $formationDispo,
                    'player_full_name'      => $p['PLAYER_FULL_NAME']  ?? null,
                    'short_name'            => $p['PLAYER_SHORT_NAME'] ?? null,
                    'player_position'       => $p['PLAYER_POSITION']   ?? null,
                    'player_rating'         => $p['PLAYER_RATING']     ?? null,
                    'player_image'          => Arr::first($p['PLAYER_IMAGES'] ?? []),
                    'is_captain'            => $p['IS_CAPTAIN']        ?? 0,
                ];

                $row = MatchLineups::updateOrCreate($key, $attrs);

                $row->wasRecentlyCreated ? $inserted++ : $updated++;
            }
        }

        Log::info("lineup:sync {$matchId} – {$inserted} inserts, {$updated} updates");
        $this->info("{$matchId}: {$inserted} inserted, {$updated} updated");

        return Command::SUCCESS;
    }
}
