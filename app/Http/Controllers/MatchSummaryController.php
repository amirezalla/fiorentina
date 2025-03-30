<?php

namespace App\Http\Controllers;

use App\Models\MatchSummary;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;


class MatchSummaryController extends Controller
{


    public function refreshMatchSummary($matchId)
{
    // 1) Remove all old records for this match
    MatchSummary::where('match_id', $matchId)->delete();

    // 2) Fetch again from the external API + regenerate JSON
    return self::storeMatchSummary($matchId);
}
    public static function storeMatchSummary($matchId)
    {
        $match=MatchSummary::where('match_id',$matchId)->first();
        if(!$match){
        // Replace this with the actual API call
        $url="https://flashlive-sports.p.rapidapi.com/v1/events/summary?locale=it_IT&event_id={$matchId}";
        $response = Http::withHeaders([
            "x-rapidapi-host" => 'flashlive-sports.p.rapidapi.com',
            "x-rapidapi-key" => '1e9b76550emshc710802be81e3fcp1a0226jsn069e6c35a2bb'
        ])->get($url);        
        
        $data = $response->json()['DATA'];

        foreach ($data as $stage) {
            if(isset($stage['ITEMS'])){
                foreach ($stage['ITEMS'] as $item) {
                    // Gather participants as a JSON structure
                    $incidentParticipants = [];
                    foreach ($item['INCIDENT_PARTICIPANTS'] as $participant) {
                        $incidentParticipants[] = [
                            'participant_name' => $participant['PARTICIPANT_NAME'] ?? null,
                            'participant_id' => $participant['PARTICIPANT_ID'] ?? null,
                            'incident_type' => $participant['INCIDENT_TYPE'] ?? null,
                            'incident_name' => $participant['INCIDENT_NAME'] ?? null,
                            'home_score' => $participant['HOME_SCORE'] ?? null,
                            'away_score' => $participant['AWAY_SCORE'] ?? null,
                        ];
                    }
    
                    // Save to the database
                    MatchSummary::updateOrCreate(
                        [
                            'match_id' => $matchId,
                            'incident_id' => $item['INCIDENT_ID'],
                        ],
                        [
                            'stage_name' => $stage['STAGE_NAME'],
                            'incident_team' => $item['INCIDENT_TEAM'],
                            'incident_time' => $item['INCIDENT_TIME'],
                            'incident_participants' => json_encode($incidentParticipants),
                            'result_home' => $stage['RESULT_HOME'] ?? null,
                            'result_away' => $stage['RESULT_AWAY'] ?? null,
                        ]
                    );
                }
            }
            
        }
    }
    self::regenerateSummaryJson($matchId);

        return response()->json(['success' => 'Match summary saved successfully.']);
    }

    private static function regenerateSummaryJson($matchId)
    {
        // 1) Fetch all match summary rows from DB
        $allSummaries = MatchSummary::where('match_id', $matchId)->get();
        // Convert them to array (or you can transform them as needed)
        $data = $allSummaries->toArray();
    
        // 2) Build the file path, e.g. "summary/summary_{matchId}.json"
        $filePath = "summary/summary_{$matchId}.json";
    
        // 3) Write to Wasabi
        Storage::put($filePath, json_encode($data));
    }


    public function showMatchSummary($matchId)
    {
        $matchSummary = MatchSummary::where('match_id', $matchId)->get();

        return view('match.summary', compact('matchSummary'));
    }


    public function getSummaryHtml($matchId)
{
    // 1) Fetch data from DB (or merged JSON) just like your main summary
    $summaries = MatchSummary::where('match_id', $matchId)->get();

    // 2) Return a partial Blade view that ONLY renders the summary HTML
    //    We'll create something like "match.partials.summary-html"
    //    and pass $summaries
    return view('diretta.partials.summary-html', compact('summaries'));
}


}
