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

public function storeCommentariesEndpoint($matchId)
{
    // 1) Call your existing static function or logic
    //    that fetches new commentary from the external API,
    //    saves to DB, updates Wasabi, etc.
    //    For example:
    self::storeCommentaries($matchId);

    // 2) Return a JSON response or whatever you prefer
    return response()->json(['status' => 'ok']);
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
