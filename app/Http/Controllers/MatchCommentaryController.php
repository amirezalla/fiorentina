<?php

namespace App\Http\Controllers;

use App\Models\MatchCommentary;
use App\Models\Calendario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use App\Jobs\StoreCommentaryJob;
use Illuminate\Support\Facades\Queue;

class MatchCommentaryController extends Controller
{

    public function fetchLatestCommentaries($matchId)
{
$this->storeCommentaries($matchId);

    // Fetch the latest commentaries ordered by time
    $commentaries = MatchCommentary::where('match_id', $matchId)
    ->where(function($query) {
        $query->whereNotNull('comment_time')
              ->orWhereNotNull('comment_class')
              ->orWhereNotNull('comment_text');
    })
    ->orderByRaw("
        CAST(SUBSTRING_INDEX(comment_time, \"'\", 1) AS UNSIGNED) + 
        IF(LOCATE('+', comment_time) > 0, 
            CAST(SUBSTRING_INDEX(SUBSTRING_INDEX(comment_time, \"'\", 1), '+', -1) AS UNSIGNED), 
            0
        )
    ")
    ->get();


    // Return JSON response
    return response()->json($commentaries);
}


public static function storeCommentariesAndRegenerateJson($matchId)
{
    // 1) Fetch from external API
    // Adjust your API key and endpoint as necessary
    $apiKey = 'YOUR_RAPIDAPI_KEY';
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
        return getCommentTimeValue($a['comment_time'] ?? '')
             <=> getCommentTimeValue($b['comment_time'] ?? '');
    });

    // This helper replicates your DB's "CAST(SUBSTRING_INDEX(...))" approach
    function getCommentTimeValue($commentTime) {
        if (empty($commentTime)) {
            return 0;
        }
        // remove trailing apostrophes, e.g. 45+2'
        $clean = rtrim($commentTime, "'");
        if (strpos($clean, '+') !== false) {
            [$main, $extra] = explode('+', $clean);
            return (int)$main + (int)$extra;
        }
        return (int)$clean;
    }

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


public static function storeCommentaries($matchId)
{
    // 1) Fetch the existing match row
    $match = Calendario::where('match_id', $matchId)->first();

    // 2) Call the external API
    $apiKey = 'YOUR_RAPIDAPI_KEY'; // example only
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



    public static function liveComments($matchId)
    {

    }
}
