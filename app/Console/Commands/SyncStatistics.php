<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use App\Models\MatchStatics;

class SyncStatistics extends Command
{
    protected $signature   = 'stats:sync {matchId}';
    protected $description = 'Pull FlashScore statistics for one match and regenerate the Wasabi JSON';

    public function handle(): int
    {
        $matchId = $this->argument('matchId');
        $apiKey  = config('services.flashscore.key');    // <— put the key in config/services.php

        $url = "https://flashlive-sports.p.rapidapi.com/v1/events/statistics"
             . "?locale=it_IT&event_id={$matchId}";

        $resp = Http::withHeaders([
                    'x-rapidapi-host' => 'flashlive-sports.p.rapidapi.com',
                    'x-rapidapi-key'  => $apiKey,
                ])->get($url);

        $stages = $resp->json('DATA') ?? [];
        if (!$stages) {
            $this->warn("No statistics returned for {$matchId}");
            return Command::SUCCESS;
        }

        /* ── 1. Purge existing rows so we always have a clean slate ── */
        MatchStatics::where('match_id', $matchId)->delete();

        /* ── 2. Flatten & upsert ── */
        foreach ($stages as $stage) {
            foreach ($stage['GROUPS'] ?? [] as $group) {
                foreach ($group['ITEMS'] ?? [] as $item) {
                    MatchStatics::create([
                        'match_id'     => $matchId,
                        'stage_name'   => $stage['STAGE_NAME'],
                        'group_label'  => $group['GROUP_LABEL'],
                        'incident_name'=> $item['INCIDENT_NAME'],
                        'value_home'   => $item['VALUE_HOME'],
                        'value_away'   => $item['VALUE_AWAY'],
                    ]);
                }
            }
        }

        /* ── 3. Regenerate Wasabi JSON ── */
        $json = MatchStatics::where('match_id', $matchId)->get()->toJson();
        Storage::disk('wasabi')->put("stats/stats_{$matchId}.json", $json, 'public');

        $this->info("Updated stats_{$matchId}.json (".strlen($json)." bytes)");
        return Command::SUCCESS;
    }
}
