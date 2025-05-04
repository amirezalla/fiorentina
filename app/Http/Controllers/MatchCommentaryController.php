<?php

namespace App\Http\Controllers;

use App\Models\MatchCommentary;
use App\Models\Calendario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use App\Jobs\StoreCommentaryJob;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\JsonResponse; // ✅ CORRECT



class MatchCommentaryController extends Controller
{

    public function fetchLatestCommentaries($matchId): JsonResponse
    {
        $path = "commentary/commentary_{$matchId}.json";
    
        if (!Storage::disk('wasabi')->exists($path)) {
            return response()->json([], 404);
        }
    
        $content = Storage::disk('wasabi')->get($path);
        $json = json_decode($content, true);
    
        return response()->json($json);
    }

    private function importFromApi( $matchId): void
    {
        $apiKey = '1e9b76550emshc710802be81e3fcp1a0226jsn069e6c35a2bb';
        $url    = "https://flashlive-sports.p.rapidapi.com/v1/events/commentary"
                . "?locale=it_IT&event_id={$matchId}";

        $resp   = Http::withHeaders([
            'x-rapidapi-host' => 'flashlive-sports.p.rapidapi.com',
            'x-rapidapi-key'  => $apiKey,
        ])->get($url);
            dd($resp->json()); // Debugging line
        $apiData = $resp->json()['DATA'] ?? [];
        if (!$apiData) {
            return;
        }

        // oldest ‑> newest so INSERT keeps order
        $apiData = array_reverse($apiData);

        foreach ($apiData as $row) {
            if (empty($row['COMMENT_TIME']) && empty($row['COMMENT_TEXT'])) {
                continue;                           // ignore blanks
            }

            // Skip if exists even in trashed
            $exists = MatchCommentary::withTrashed()
                        ->where('match_id', $matchId)
                        ->where('comment_time', $row['COMMENT_TIME'] ?? null)
                        ->where('comment_text', $row['COMMENT_TEXT'] ?? null)
                        ->exists();

            if ($exists) continue;

            MatchCommentary::create([
                'match_id'      => $matchId,
                'comment_time'  => $row['COMMENT_TIME'] ?? null,
                'comment_class' => $row['COMMENT_CLASS'] ?? null,
                'comment_text'  => $row['COMMENT_TEXT'] ?? null,
                'is_bold'       => $row['COMMENT_IS_BOLD'] ?? 0,
                'is_important'  => $row['COMMENT_IS_IMPORTANT'] ?? 0,
            ]);
        }

        // rewrite Wasabi JSON once
        $this->regenerateJson($matchId);
    }

public static function storeCommentariesAndRegenerateJson($matchId)
{
    // 1) Fetch from external API
    // Adjust your API key and endpoint as necessary
    $apiKey = '1e9b76550emshc710802be81e3fcp1a0226jsn069e6c35a2bb';
    $url = "https://flashlive-sports.p.rapidapi.com/v1/events/commentary?locale=it_IT&event_id={$matchId}";

    $response = Http::withHeaders([
        'x-rapidapi-host' => 'flashlive-sports.p.rapidapi.com',
        'x-rapidapi-key' => $apiKey
    ])->get($url);

    // The commentary data from the external source
    $apiCommentaries = $response->json()['DATA'] ?? [];

    // 2) Fetch from DB (admin-inserted or existing)
    // Adjust or remove condition if you want also 'empty' comment_time or text
    $dbCommentaries = MatchCommentary::where('match_id', $matchId)
        ->get()
        ->toArray();

    // 3) Merge them, optionally avoiding duplicates
    // We'll unify them in a single array. 
    // a) Convert $apiCommentaries to a consistent structure
    // b) Then combine with $dbCommentaries
    // c) If needed, deduplicate by building a "unique key" from comment_time + comment_text

    // Convert each API item to the same keys as DB
    // e.g. for DB we have: comment_time, comment_class, comment_text, is_bold, is_important...
    $formattedApi = [];
    foreach ($apiCommentaries as $comment) {
        // skip truly empty
        if (empty($comment['COMMENT_TIME']) && empty($comment['COMMENT_TEXT'])) {
            continue;
        }
        $formattedApi[] = [
            'match_id'      => $matchId,
            'comment_time'  => $comment['COMMENT_TIME']     ?? null,
            'comment_class' => $comment['COMMENT_CLASS']    ?? null,
            'comment_text'  => $comment['COMMENT_TEXT']     ?? null,
            'is_bold'       => $comment['COMMENT_IS_BOLD']  ?? 0,
            'is_important'  => $comment['COMMENT_IS_IMPORTANT'] ?? 0,
        ];
    }

    // Build a merged array
    $merged = array_merge($dbCommentaries, $formattedApi);

    // Deduplicate by building a unique key for each item
    // Example:  "<time>#<text>"
    // Adjust as needed if your data can differ in other fields
    $uniqueMap = [];
    foreach ($merged as $item) {
        // If you stored your DB commentary in the same fields, this works:
        $time   = $item['comment_time'] ?? '';
        $text   = $item['comment_text'] ?? '';
        $unique = $time . '#' . $text; 

        // store if not seen
        if (!isset($uniqueMap[$unique])) {
            $uniqueMap[$unique] = $item;
        }
    }

    // Now $uniqueMap has unique commentary. Convert it back to a normal array
    $finalCommentaries = array_values($uniqueMap);

    // 4) (Optional) Sort them by comment_time if you want chronological order
    //    replicating your SQL logic. For example:
    usort($finalCommentaries, function($a, $b) {
        return self::getCommentTimeValue($a['comment_time'] ?? '')
        <=> self::getCommentTimeValue($b['comment_time'] ?? '');
    });


    // 5) Overwrite JSON in Wasabi (so your front end sees the updated commentary)
    $filePath = "commentary/commentary_{$matchId}.json";
    Storage::put($filePath, json_encode($finalCommentaries));

    // At this point, the WebSocket server will detect a changed ETag and broadcast

    // (Optional) Return some info
    return response()->json([
        'message' => 'Commentaries merged and JSON regenerated successfully',
        'count_final' => count($finalCommentaries),
    ]);
}

private static function getCommentTimeValue($commentTime)
{
    if (empty($commentTime)) {
        return 0;
    }

    // Remove trailing apostrophes, e.g. "45+3'"
    $clean = rtrim($commentTime, "'");

    // If there's a plus sign, parse main minute and extra
    if (strpos($clean, '+') !== false) {
        [$main, $extra] = explode('+', $clean);
        $mainVal  = (int) $main;
        $extraVal = (int) $extra;
        // e.g. 45+3 => 45.003, 90+2 => 90.002
        return $mainVal + ($extraVal / 1000);
    }

    // Otherwise just convert to int (e.g. "46'" => 46)
    return (int) $clean;
}

private function regenerateJson( $matchId): void
{
    $content = MatchCommentary::where('match_id', $matchId)
               ->orderBy('id', 'desc')
               ->get()
               ->toJson();

    Storage::put("commentary/commentary_{$matchId}.json", $content);
}

public static function storeCommentaries($matchId)
{
    // 1) Fetch the existing match row
    $match = Calendario::where('match_id', $matchId)->first();

    // 2) Call the external API
    $apiKey = '1e9b76550emshc710802be81e3fcp1a0226jsn069e6c35a2bb'; // example only
    $url = "https://flashlive-sports.p.rapidapi.com/v1/events/commentary?locale=it_IT&event_id={$matchId}";

    $response = Http::withHeaders([
        'x-rapidapi-host' => 'flashlive-sports.p.rapidapi.com',
        'x-rapidapi-key' => $apiKey
    ])->get($url);

    // 3) Assuming the response returns the data in ['DATA']...
    $data = $response->json()['DATA'] ?? [];
    if (empty($data)) {
        return response()->json(['info' => 'No commentary data found from API']);
    }

    // 4) Reverse the array so we process oldest -> newest
    $data = array_reverse($data);

    // 5) For each item, check if we already have it in DB
    $insertedCount = 0;
    foreach ($data as $comment) {
        // Optionally skip if it has no meaningful fields:
        if (empty($comment['COMMENT_TIME']) && empty($comment['COMMENT_TEXT'])) {
            continue; // skip items that are basically empty
        }

        // Check if we already stored this exact comment
        // Using match_id + comment_time + comment_text
        // Adjust to match your actual logic (some events might not have comment_time)
        $exists = MatchCommentary::where('match_id', $matchId)
            ->where('comment_time', $comment['COMMENT_TIME'] ?? null)
            ->where('comment_text', $comment['COMMENT_TEXT'] ?? null)
            ->exists();
        if ($exists) {
            // already in DB, skip
            continue;
        }

        // Not in DB yet, so create
        $newItem = MatchCommentary::create([
            'match_id'      => $matchId,
            'comment_time'  => $comment['COMMENT_TIME']     ?? null,
            'comment_class' => $comment['COMMENT_CLASS']    ?? null,
            'comment_text'  => $comment['COMMENT_TEXT']     ?? null,
            'is_bold'       => $comment['COMMENT_IS_BOLD']  ?? 0,
            'is_important'  => $comment['COMMENT_IS_IMPORTANT'] ?? 0,
        ]);

        // Optionally queue a job to update your Wasabi JSON
        Queue::push(new StoreCommentaryJob($newItem->toArray()));

        $insertedCount++;
    }

    // 6) Update commentary_count, if desired (optional)
    // e.g. $match->commentary_count = MatchCommentary::where('match_id', $matchId)->count();
    // $match->save();

    return response()->json([
        'success' => 'Commentary sync completed',
        'inserted_count' => $insertedCount,
    ]);
}


/**
 * Import commentary from RapidAPI, but never overwrite a row that
 * an admin has already edited (updated_at ≠ created_at).
 */
public function importFromApi1($matchId): void
{
    /* -------------------------------------------------- 1. Load API data */
    $resp = Http::withHeaders([
                'x-rapidapi-host' => 'flashlive-sports.p.rapidapi.com',
                'x-rapidapi-key'  => '1e9b76550emshc710802be81e3fcp1a0226jsn069e6c35a2bb',
            ])->get("https://flashlive-sports.p.rapidapi.com/v1/events/commentary"
                   . "?locale=it_IT&event_id={$matchId}");

    $apiData = $resp->json()['DATA'] ?? [];
    if (!$apiData) {
        return;
    }

    $apiData = array_reverse($apiData);             // oldest → newest

    /* -------------------------------------------------- 2. Process rows  */
    foreach ($apiData as $row) {
        if (empty($row['COMMENT_TIME']) && empty($row['COMMENT_TEXT'])) continue;

        $minute = $row['COMMENT_TIME']  ?? null;
        $class  = $row['COMMENT_CLASS'] ?? null;
        $text   = $row['COMMENT_TEXT']  ?? null;

        /* 2‑a  Was an admin edit already made for this minute ? */
        $editedExists = MatchCommentary::where('match_id', $matchId)
                        ->where('comment_time', $minute)
                        ->whereColumn('updated_at', '!=', 'created_at')
                        ->exists();

        if ($editedExists) {
            // respect admin edit – do not import/overwrite anything for this minute
            continue;
        }

        /* 2‑b  Check for existing row with the same composite key */
        $key = [
            'match_id'      => $matchId,
            'comment_time'  => $minute,
            'comment_class' => $class,
            'comment_text'  => $text,
        ];

        $existing = MatchCommentary::withTrashed()
                    ->where($key)
                    ->first();

        // If the exact composite row exists but is soft‑deleted, keep it deleted
        if ($existing && $existing->trashed()) {
            continue;
        }

        // If active row exists, update meta only (keep text)
        if ($existing) {
            $existing->update([
                'is_bold'      => $row['COMMENT_IS_BOLD']      ?? $existing->is_bold,
                'is_important' => $row['COMMENT_IS_IMPORTANT'] ?? $existing->is_important,
            ]);
            continue;
        }

        /* 2‑c  Otherwise create a brand‑new commentary */
        MatchCommentary::create(array_merge($key, [
            'is_bold'      => $row['COMMENT_IS_BOLD']      ?? 0,
            'is_important' => $row['COMMENT_IS_IMPORTANT'] ?? 0,
        ]));
    }

    /* -------------------------------------------------- 3. Rewrite JSON  */
    $content = MatchCommentary::where('match_id', $matchId)
               ->orderBy('id', 'desc')
               ->get()
               ->toJson();

    Storage::put("commentary/commentary_{$matchId}.json", $content);
}


}
