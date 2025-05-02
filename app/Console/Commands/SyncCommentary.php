<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use App\Models\Calendario;          // ← your “matches” table
use App\Models\MatchCommentary;     // ← your commentary model
use Illuminate\Support\Facades\Log; // ← for logging

class SyncCommentary extends Command
{
    protected $signature   = 'commentary:sync {matchId}';
    protected $description = 'Pull FlashScore commentary for one match and regenerate the Wasabi JSON';

    public function handle(): int
    {
        $matchId = $this->argument('matchId');
        $apiKey  = '1e9b76550emshc710802be81e3fcp1a0226jsn069e6c35a2bb';

        $url = "https://flashlive-sports.p.rapidapi.com/v1/events/commentary"
             . "?locale=it_IT&event_id={$matchId}";

        $resp     = Http::withHeaders([
                        'x-rapidapi-host' => 'flashlive-sports.p.rapidapi.com',
                        'x-rapidapi-key'  => $apiKey,
                    ])->get($url);

        $apiRows  = $resp->json('DATA') ?? [];
        if (!$apiRows) {
            $this->warn("No data returned for {$matchId}");
            return Command::SUCCESS;
        }

        /* ─── 1. Reverse → oldest-first so INSERT preserves order ─── */
        $apiRows = array_reverse($apiRows);

        /* ─── 2. Insert / update DB without touching admin-edited rows ─── */
        foreach ($apiRows as $row) {
            if (empty($row['COMMENT_TIME']) && empty($row['COMMENT_TEXT'])) continue;
        
            // Normalise the two flags (null → 0) so the key is consistent
            $flagBold      = $row['COMMENT_IS_BOLD']      ?? 0;
            $flagImportant = $row['COMMENT_IS_IMPORTANT'] ?? 0;
        
            // Key that makes a commentary UNIQUE for your rules
            $key = [
                'match_id'      => $matchId,
                'comment_time'  => $row['COMMENT_TIME']  ?? null,
                'comment_class' => $row['COMMENT_CLASS'] ?? null,
                'is_bold'       => $flagBold,
                'is_important'  => $flagImportant,
            ];
        
            // Skip rows that an admin has already modified
            $edited = MatchCommentary::where($key)
                      ->whereColumn('updated_at', '!=', 'created_at')
                      ->exists();
            if ($edited) continue;
        
            // Update existing row (text may have changed) or create a new one
            MatchCommentary::updateOrCreate($key, [
                'comment_text' => $row['COMMENT_TEXT'] ?? null,
            ]);
        }
        DB::statement("
    DELETE t1 FROM match_commentaries t1
    JOIN match_commentaries t2
      ON  t1.match_id      = t2.match_id
      AND t1.comment_time  = t2.comment_time
      AND COALESCE(t1.comment_class,'')  = COALESCE(t2.comment_class,'')
      AND t1.is_bold       = t2.is_bold
      AND t1.is_important  = t2.is_important
      AND t1.id            > t2.id    -- keep the first (lowest id) row
");

        /* ─── 3. Dump the fresh commentary to Wasabi ─── */
        $minuteExpr = "
        CAST(SUBSTRING_INDEX(comment_time, '+', 1) AS UNSIGNED) +
        IF(LOCATE('+', comment_time) > 0,
           CAST(REPLACE(SUBSTRING_INDEX(comment_time, '+', -1), \"'\", '') AS UNSIGNED),
           0)
    ";
    
    // pull newest‑to‑oldest so clients can just read top‑down
    $json = MatchCommentary::where('match_id', $matchId)
            ->orderByRaw("$minuteExpr DESC")
            ->orderBy('id', 'DESC')        // tie‑breaker
            ->get()
            ->toJson(JSON_UNESCAPED_UNICODE);

        Log::info($json);
        Storage::disk('wasabi')->put("commentary/commentary_{$matchId}.json", $json, 'public');

        $this->info("Updated commentary_{$matchId}.json (".strlen($json)." bytes)");
        return Command::SUCCESS;
    }
}
