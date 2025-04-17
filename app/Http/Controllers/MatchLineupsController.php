<?php
namespace App\Http\Controllers;

use App\Models\MatchLineups;
use App\Models\Calendario;
use App\Models\PlayerStats;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Carbon\Carbon;

class MatchLineupsController extends Controller
{
    public static function getPlayerStats($matchId, $player)
    {
        $playerStats=PlayerStats::where('match_id',$matchId)->where('player_id',$player->player_id)->first();
        if(!$playerStats){
        // Replace with your actual API key and endpoint
        $apiKey = '1e9b76550emshc710802be81e3fcp1a0226jsn069e6c35a2bb';
        $url = "https://flashlive-sports.p.rapidapi.com/v1/players/alt-events?player_id={$player->player_id}&sport_id=1&locale=it_IT";
    
        // Send API request to retrieve player's stats
        $response = Http::withHeaders([
            "x-rapidapi-host" => "flashlive-sports.p.rapidapi.com",
            "x-rapidapi-key" => $apiKey
        ])->get($url);
    
        // Ensure a successful response
        if ($response->failed()) {
            return response()->json(['error' => 'Unable to fetch player stats'], 500);
        }
    
        // Parse response data
        $data = $response->json()['DATA'];
    
        // Loop through the DATA array to find the relevant match stats
        foreach ($data as $event) {
            // Check if the EVENT_ENCODED_ID matches the provided matchId
            if ($event['EVENT_ENCODED_ID'] === $matchId) {
                // Extract stats and save them to the PlayerStats model
                $playerStats = new PlayerStats();
                $playerStats->match_id = $matchId;
                $playerStats->player_id = $player->player_id; // Assuming player_id is a field in the player object
                $playerStats->stats = json_encode($event['STATS']); // Save stats as JSON
                $playerStats->rating = $event['RATING'] ?? null; // Optional: Save player rating if available
                $playerStats->save();
    
                return $playerStats->stats = json_encode($event['STATS']);
            }
        }
    
        // If no matching event is found
        return response()->json(['error' => 'No stats found for the given match'], 404);
        }else{
            return $playerStats->stats;
        }

    }
    



    private static function getLineup($matchId) {

        $match = Calendario::where('match_id', $matchId)->first();
        $homeTeam = json_decode($match->home_team, true);
        $isHomeFiorentina = 
            $homeTeam['name'] == 'Fiorentina' || 
            $homeTeam['name'] == 'Fiorentina (Ita)' || 
            $homeTeam['name'] == 'Fiorentina (Ita) *';
    
        $url = "https://flashlive-sports.p.rapidapi.com/v1/events/lineups?locale=it_IT&event_id={$matchId}";
        $response = Http::withHeaders([
            "x-rapidapi-host" => 'flashlive-sports.p.rapidapi.com',
            "x-rapidapi-key" => '1e9b76550emshc710802be81e3fcp1a0226jsn069e6c35a2bb'
        ])->get($url);
    
        // Extract the data from the response
        $data = $response->json()['DATA'];
    
        // Coach Data
        $fiorentinaCoachData = $isHomeFiorentina ? $data[2]['FORMATIONS'][0] : $data[2]['FORMATIONS'][1];
        $anotherCoachData = $isHomeFiorentina ? $data[2]['FORMATIONS'][1] : $data[2]['FORMATIONS'][0];
    
        // Subs Data
        $fiorentinaSubsData = $isHomeFiorentina ? $data[1]['FORMATIONS'][0] : $data[1]['FORMATIONS'][1];
        $anotherSubsData = $isHomeFiorentina ? $data[1]['FORMATIONS'][1] : $data[1]['FORMATIONS'][0];
    
        // Initial Lineup Data
        $fiorentinaInitialData = $isHomeFiorentina ? $data[0]['FORMATIONS'][0] : $data[0]['FORMATIONS'][1];
        $anotherInitialData = $isHomeFiorentina ? $data[0]['FORMATIONS'][1] : $data[0]['FORMATIONS'][0];
    
        // Add Formation Names
        $fiorentinaCoachData['FORMATION_NAME'] = 'Fiorentina Coach';
        $anotherCoachData['FORMATION_NAME'] = 'Another Coach';
        $fiorentinaSubsData['FORMATION_NAME'] = 'Fiorentina Subs';
        $anotherSubsData['FORMATION_NAME'] = 'Another Subs';
        $fiorentinaInitialData['FORMATION_NAME'] = 'Fiorentina Initial Lineup';
        $anotherInitialData['FORMATION_NAME'] = 'Another Initial Lineup';
    
        return [
            'FiorentinaCoach' => $fiorentinaCoachData,
            'AnotherCoach' => $anotherCoachData,
            'FiorentinaSub' => $fiorentinaSubsData,
            'AnotherSub' => $anotherSubsData,
            'FiorentinaInitial' => $fiorentinaInitialData,
            'AnotherInitial' => $anotherInitialData
        ];
    }
    

    public static function storeLineups($matchId)
    {
        $match=MatchLineups::where('match_id',$matchId)->first();
        if(!$match){
            $data=MatchLineupsController::getLineup($matchId);
            dd($data);
            foreach ($data as $category) {
                foreach ($category['MEMBERS'] as $player) {
                    MatchLineups::updateOrCreate(
                        [
                            'match_id' => $matchId,
                            'player_id' => $player['PLAYER_ID']
                        ],
                        [
                            'formation_name' => $category['FORMATION_NAME'],
                            'formation_disposition' => $category['FORMATION_DISPOSTION'] ?? null,
                            'player_full_name' => $player['PLAYER_FULL_NAME'],
                            'player_position' => $player['PLAYER_POSITION'] ?? null,
                            'player_number' => $player['PLAYER_NUMBER'] ?? null,
                            'player_country' => $player['PLAYER_COUNTRY'],
                            'player_rating' => $player['LPR'] ?? null,
                            'short_name' => $player['SHORT_NAME'],
                            'player_image' =>  isset($player['LPI']) ? "https://static.flashscore.com/res/image/data/{$player['LPI']}" : null
                        ]
                    );
                }
            }
        }
        

        return response()->json(['success' => 'Lineups saved successfully']);
    }
}
