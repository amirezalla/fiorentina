<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use App\Models\MatchSummary;

class SyncRiassunto extends Command
{
    protected $signature   = 'summary:sync {matchId}';
    protected $description = 'Pull FlashScore summary (riassunto) for one match and regenerate the Wasabi JSON';

    public function handle(): int
    {
        $matchId = $this->argument('matchId');
        $apiKey  = config('services.flashscore.key');

        $url = "https://flashlive-sports.p.rapidapi.com/v1/events/summary"
             . "?locale=it_IT&event_id={$matchId}";

        $resp = Http::withHeaders([
                    'x-rapidapi-host' => 'flashlive-sports.p.rapidapi.com',
                    'x-rapidapi-key'  => $apiKey,
                ])->get($url);

        $stages = $resp->json('DATA') ?? [];
        if (!$stages) {
            $this->warn("No summary returned for {$matchId}");
            return Command::SUCCESS;
        }

        /* ── 1. Wipe old rows ── */
        MatchSummary::where('match_id', $matchId)->delete();

        /* ── 2. Insert ── */
        foreach ($stages as $stage) {
            foreach ($stage['ITEMS'] ?? [] as $item) {
                $participants = collect($item['INCIDENT_PARTICIPANTS'] ?? [])
                                ->map(fn($p) => Arr::only($p, [
                                    'PARTICIPANT_NAME','PARTICIPANT_ID',
                                    'INCIDENT_TYPE','INCIDENT_NAME',
                                    'HOME_SCORE','AWAY_SCORE',
                                ]))
                                ->values()
                                ->all();

                MatchSummary::create([
                    'match_id'            => $matchId,
                    'incident_id'         => $item['INCIDENT_ID'],
                    'stage_name'          => $stage['STAGE_NAME'],
                    'incident_team'       => $item['INCIDENT_TEAM'],
                    'incident_time'       => $item['INCIDENT_TIME'],
                    'incident_participants'=> json_encode($participants),
                    'result_home'         => $stage['RESULT_HOME'] ?? null,
                    'result_away'         => $stage['RESULT_AWAY'] ?? null,
                ]);
            }
        }

        /* ── 3. Wasabi JSON ── */
        $json = MatchSummary::where('match_id', $matchId)->get()->toJson();
        Storage::disk('wasabi')->put("summary/summary_{$matchId}.json", $json, 'public');

        $this->info("Updated summary_{$matchId}.json (".strlen($json)." bytes)");
        return Command::SUCCESS;
    }
}
