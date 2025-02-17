<?php


namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Standing;
use App\Models\Matches;
use App\Models\Calendario;
use App\Models\CalendarioPV;
use Illuminate\Support\Facades\Http;
use Carbon\Carbon;
// sportmonks B0lZqWEdqBzEPrLW5gDcm87Svgb5bnEEa807fd7kOiONHbcbetXywqPQafqC

class StandingController extends Controller
{
    public static function fetchStandingsIfNeeded()
    {
        $latestUpdate = Standing::latest('updated_at')->first();
    
        // Check if the data was updated within the last 20 hours or if the table is empty
        if (!$latestUpdate || $latestUpdate->updated_at <= Carbon::now()->subHours(20)) {
    
            // Clear the existing standings table
            Standing::truncate();
    
            $response = Http::withHeaders([
                'X-Auth-Token' => 'e1ef65752c2b42c2b8002bccec730215' 
            ])->get('http://api.football-data.org/v4/competitions/SA/standings');
    
            if ($response->successful()) {
                $standings = $response->json()['standings'][0]['table']; // Adjust based on actual JSON structure
    
                foreach ($standings as $standing) {
                    Standing::updateOrCreate(
                        ['team_id' => $standing['team']['id']], // Assuming team_id is unique and consistent
                        [
                            'position' => $standing['position'],
                            'team_name' => $standing['team']['name'],
                            'short_name' => $standing['team']['shortName'],
                            'tla' => $standing['team']['tla'],
                            'crest_url' => $standing['team']['crest'],
                            'played_games' => $standing['playedGames'],
                            'form' => $standing['form'],
                            'won' => $standing['won'],
                            'draw' => $standing['draw'],
                            'lost' => $standing['lost'],
                            'points' => $standing['points'],
                            'goals_for' => $standing['goalsFor'],
                            'goals_against' => $standing['goalsAgainst'],
                            'goal_difference' => $standing['goalDifference']
                        ]
                    );
                }
    
                return "Standings updated successfully.";
            } else {
                return "Failed to fetch standings. Error: " . $response->body();
            }
        }
    
        return "No update needed.";
    }
    

    public static function fetchScheduledMatches()
    {
        $latestUpdate = Matches::where('status', 'TIMED')->latest('updated_at')->first();
        if (!$latestUpdate || $latestUpdate->updated_at <= Carbon::now()->subHours(20)) {
        $response = Http::withHeaders([
            'X-Auth-Token' => 'e1ef65752c2b42c2b8002bccec730215'
        ])->get('https://api.football-data.org/v4/teams/99/matches', [
            'status' => 'SCHEDULED'
        ]);

        $matches = $response->json()['matches'];

        // Check if there is at least one match and only process the first one
        if (!empty($matches)) {
            $match = $matches[0];  // Get the first match
            $matchDate = Carbon::parse($match['utcDate'])->format('Y-m-d H:i:s');
            Matches::updateOrCreate(
                ['match_id' => $match['id']],
                [
                    'venue' => $match['venue'] ?? null,
                    'matchday' => $match['matchday'],
                    'stage' => $match['stage'],
                    'group' => $match['group'] ?? null,
                    'match_date' => $matchDate,  // Use the formatted date
                    'status' => $match['status'],
                    'home_team' => json_encode($match['homeTeam'] ?? []),
                    'away_team' => json_encode($match['awayTeam'] ?? []),
                    'score' => json_encode($match['score'] ?? []),
                    'goals' => !empty($match['goals']) ? json_encode($match['goals']) : null,
                    'penalties' => !empty($match['penalties']) ? json_encode($match['penalties']) : null,
                    'bookings' => !empty($match['bookings']) ? json_encode($match['bookings']) : null,
                    'substitutions' => !empty($match['substitutions']) ? json_encode($match['substitutions']) : null,
                    'odds' => !empty($match['odds']) ? json_encode($match['odds']) : null,
                    'referees' => !empty($match['referees']) ? json_encode($match['referees']) : null,
                ]
            );
            return "First timed match updated successfully.";
        }
        }
        return "No update needed or no matches found.";

    }

    public static function fetchFinishedMatches()
    {
        $latestUpdate = Matches::where('status','FINISHED')->latest('updated_at')->first();
        if (!$latestUpdate || $latestUpdate->updated_at <= Carbon::now()->subHours(20)) {
            $response = Http::withHeaders([
                'X-Auth-Token' => 'e1ef65752c2b42c2b8002bccec730215'
            ])->get('https://api.football-data.org/v4/teams/99/matches', [
                'status' => 'FINISHED'
            ]);

            $matches = $response->json()['matches'];

            foreach ($matches as $match) {
                Matches::updateOrCreate(
                    ['match_id' => $match['id']],
                    [
                        'venue' => $match['venue'],
                        'matchday' => $match['matchday'],
                        'stage' => $match['stage'],
                        'group' => $match['group'],
                        'match_date' => $match['utcDate'],
                        'status' => $match['status'],
                        'home_team' => json_encode($match['homeTeam']),
                        'away_team' => json_encode($match['awayTeam']),
                        'score' => json_encode($match['score']),
                        'goals' => json_encode($match['goals']),
                        'penalties' => json_encode($match['penalties']),
                        'bookings' => json_encode($match['bookings']),
                        'substitutions' => json_encode($match['substitutions']),
                        'odds' => json_encode($match['odds']),
                        'referees' => json_encode($match['referees']),
                    ]
                );
            }
            return "Matches updated successfully.";
        }
            return "No update needed.";

    }

    public static function FetchCalendarioPV(){
        // Fetch the latest update based on match status
        $latestUpdate = CalendarioPV::where('status', 'TIMED')->latest('updated_at')->first();

        // Check if the last update was more than 10 hours ago
        if (!$latestUpdate || $latestUpdate->updated_at <= Carbon::now()->subHours(10)) {
        // if (1) {
        Standing::truncate();

        // Make the API request with necessary headers
        $response = Http::withHeaders([
            "x-rapidapi-host" => 'flashlive-sports.p.rapidapi.com',
            "x-rapidapi-key" => '1e9b76550emshc710802be81e3fcp1a0226jsn069e6c35a2bb'
        ])->get('https://flashlive-sports.p.rapidapi.com/v1/teams/fixtures?locale=it_IT&sport_id=1&team_id=Q9VVMjSi');

        // Extract the data from the response
        $data = $response->json()['DATA'];

        foreach($data as $tournament){
            foreach ($tournament['EVENTS'] as $match) {
                // Parse the match date from the timestamp
                $matchDate = Carbon::createFromTimestamp($match['START_TIME'])->format('Y-m-d H:i:s');

                // Prepare the home and away team information
                $homeTeam = [
                    'id' => $match['HOME_PARTICIPANT_IDS'][0] ?? null,
                    'name' => $match['HOME_NAME'],
                    'shortname' => $match['SHORTNAME_HOME'],
                    'slug' => $match['HOME_SLUG'],
                    'logo' => $match['HOME_IMAGES'][0] ?? null,
                ];

                $awayTeam = [
                    'id' => $match['AWAY_PARTICIPANT_IDS'][0] ?? null,
                    'name' => $match['AWAY_NAME'],
                    'shortname' => $match['SHORTNAME_AWAY'],
                    'slug' => $match['AWAY_SLUG'],
                    'logo' => $match['AWAY_IMAGES'][0] ?? null,
                ];

                // Update or create match entry in the 'calendario' table
                CalendarioPV::updateOrCreate(
                    ['match_id' => $match['EVENT_ID']],  // Use the unique match/event ID
                    [
                        'venue' => null,  // Venue data doesn't seem to be present in your response
                        'matchday' => $match['ROUND'] ?? 'Unknown',
                        'competition' => $tournament['TOURNAMENT_IMAGE'],  // Use the tournament image as competition reference
                        'group' => $tournament['NAME_PART_2'],  // Example: Serie A
                        'match_date' => $matchDate,  // Formatted match date
                        'status' => $match['STAGE_TYPE'],
                        'home_team' => json_encode($homeTeam),
                        'away_team' => json_encode($awayTeam),
                        'score' => json_encode([
                            'home' => $match['HOME_SCORE_CURRENT'] ?? 0,
                            'away' => $match['AWAY_SCORE_CURRENT'] ?? 0,
                        ]),
                        'goals' => null,  // The API response does not provide detailed goals
                        'penalties' => null,  // The API response does not provide penalties
                        'bookings' => null,  // The API response does not provide bookings
                        'substitutions' => null,  // The API response does not provide substitutions
                        'odds' => null,  // Odds can be filled if available
                        'referees' => null,  // Referee information is missing in this response
                    ]
                );
            }
        }



        // Make the API request with necessary headers
        $response = Http::withHeaders([
            "x-rapidapi-host" => 'flashlive-sports.p.rapidapi.com',
            "x-rapidapi-key" => '1e9b76550emshc710802be81e3fcp1a0226jsn069e6c35a2bb'
        ])->get('https://flashlive-sports.p.rapidapi.com/v1/teams/results?sport_id=1&locale=it_IT&team_id=Q3A3IbXH');

// Extract the data from the response
$data = $response->json()['DATA'];

foreach($data as $tournament){
    foreach ($tournament['EVENTS'] as $match) {
        // Parse the match date from the timestamp
        $matchDate = Carbon::createFromTimestamp($match['START_TIME'])->format('Y-m-d H:i:s');

        // Prepare the home and away team information
        $homeTeam = [
            'id' => $match['HOME_PARTICIPANT_IDS'][0] ?? null,
            'name' => $match['HOME_NAME'],
            'shortname' => $match['SHORTNAME_HOME'],
            'slug' => $match['HOME_SLUG'],
            'logo' => $match['HOME_IMAGES'][0] ?? null,
        ];

        $awayTeam = [
            'id' => $match['AWAY_PARTICIPANT_IDS'][0] ?? null,
            'name' => $match['AWAY_NAME'],
            'shortname' => $match['SHORTNAME_AWAY'],
            'slug' => $match['AWAY_SLUG'],
            'logo' => $match['AWAY_IMAGES'][0] ?? null,
        ];

        // Update or create match entry in the 'calendario' table
        CalendarioPV::updateOrCreate(
            ['match_id' => $match['EVENT_ID']],  // Use the unique match/event ID
            [
                'venue' => null,  // Venue data doesn't seem to be present in your response
                'matchday' => $match['ROUND'] ?? 'Unknown',
                'competition' => $tournament['TOURNAMENT_IMAGE'],  // Use the tournament image as competition reference
                'group' => $tournament['NAME_PART_2'],  // Example: Serie A
                'match_date' => $matchDate,  // Formatted match date
                'status' => $match['STAGE_TYPE'],
                'home_team' => json_encode($homeTeam),
                'away_team' => json_encode($awayTeam),
                'score' => json_encode([
                    'home' => $match['HOME_SCORE_CURRENT'] ?? 0,
                    'away' => $match['AWAY_SCORE_CURRENT'] ?? 0,
                ]),
                'goals' => null,  // The API response does not provide detailed goals
                'penalties' => null,  // The API response does not provide penalties
                'bookings' => null,  // The API response does not provide bookings
                'substitutions' => null,  // The API response does not provide substitutions
                'odds' => null,  // Odds can be filled if available
                'referees' => null,  // Referee information is missing in this response
            ]
        );
    }
}

        // Loop through the response data to get fixtures and match information


        // Return a success message
        return "Fixtures updated.";
    }
    }


    public static function FetchCalendario()
    {
        // Fetch the latest update based on match status
        $latestUpdate = Calendario::where('status', 'TIMED')->latest('updated_at')->first();

        // Check if the last update was more than 10 hours ago
        if (!$latestUpdate || $latestUpdate->updated_at <= Carbon::now()->subHours(10)) {
        // if (1) {
        Standing::truncate();

        // Make the API request with necessary headers
        $response = Http::withHeaders([
            "x-rapidapi-host" => 'flashlive-sports.p.rapidapi.com',
            "x-rapidapi-key" => '1e9b76550emshc710802be81e3fcp1a0226jsn069e6c35a2bb'
        ])->get('https://flashlive-sports.p.rapidapi.com/v1/teams/fixtures?locale=it_IT&sport_id=1&team_id=Q3A3IbXH');

        // Extract the data from the response
        $data = $response->json()['DATA'];

        foreach($data as $tournament){
            foreach ($tournament['EVENTS'] as $match) {
                // Parse the match date from the timestamp
                $matchDate = Carbon::createFromTimestamp($match['START_TIME'])->format('Y-m-d H:i:s');

                // Prepare the home and away team information
                $homeTeam = [
                    'id' => $match['HOME_PARTICIPANT_IDS'][0] ?? null,
                    'name' => $match['HOME_NAME'],
                    'shortname' => $match['SHORTNAME_HOME'],
                    'slug' => $match['HOME_SLUG'],
                    'logo' => $match['HOME_IMAGES'][0] ?? null,
                ];

                $awayTeam = [
                    'id' => $match['AWAY_PARTICIPANT_IDS'][0] ?? null,
                    'name' => $match['AWAY_NAME'],
                    'shortname' => $match['SHORTNAME_AWAY'],
                    'slug' => $match['AWAY_SLUG'],
                    'logo' => $match['AWAY_IMAGES'][0] ?? null,
                ];

                // Update or create match entry in the 'calendario' table
                Calendario::updateOrCreate(
                    ['match_id' => $match['EVENT_ID']],  // Use the unique match/event ID
                    [
                        'venue' => null,  // Venue data doesn't seem to be present in your response
                        'matchday' => $match['ROUND'] ?? 'Unknown',
                        'competition' => $tournament['TOURNAMENT_IMAGE'],  // Use the tournament image as competition reference
                        'group' => $tournament['NAME_PART_2'],  // Example: Serie A
                        'match_date' => $matchDate,  // Formatted match date
                        'status' => $match['STAGE_TYPE'],
                        'home_team' => json_encode($homeTeam),
                        'away_team' => json_encode($awayTeam),
                        'score' => json_encode([
                            'home' => $match['HOME_SCORE_CURRENT'] ?? 0,
                            'away' => $match['AWAY_SCORE_CURRENT'] ?? 0,
                        ]),
                        'goals' => null,  // The API response does not provide detailed goals
                        'penalties' => null,  // The API response does not provide penalties
                        'bookings' => null,  // The API response does not provide bookings
                        'substitutions' => null,  // The API response does not provide substitutions
                        'odds' => null,  // Odds can be filled if available
                        'referees' => null,  // Referee information is missing in this response
                    ]
                );
            }
        }



        // Make the API request with necessary headers
        $response = Http::withHeaders([
            "x-rapidapi-host" => 'flashlive-sports.p.rapidapi.com',
            "x-rapidapi-key" => '1e9b76550emshc710802be81e3fcp1a0226jsn069e6c35a2bb'
        ])->get('https://flashlive-sports.p.rapidapi.com/v1/teams/results?sport_id=1&locale=it_IT&team_id=Q3A3IbXH');

// Extract the data from the response
$data = $response->json()['DATA'];

foreach($data as $tournament){
    foreach ($tournament['EVENTS'] as $match) {
        // Parse the match date from the timestamp
        $matchDate = Carbon::createFromTimestamp($match['START_TIME'])->format('Y-m-d H:i:s');

        // Prepare the home and away team information
        $homeTeam = [
            'id' => $match['HOME_PARTICIPANT_IDS'][0] ?? null,
            'name' => $match['HOME_NAME'],
            'shortname' => $match['SHORTNAME_HOME'],
            'slug' => $match['HOME_SLUG'],
            'logo' => $match['HOME_IMAGES'][0] ?? null,
        ];

        $awayTeam = [
            'id' => $match['AWAY_PARTICIPANT_IDS'][0] ?? null,
            'name' => $match['AWAY_NAME'],
            'shortname' => $match['SHORTNAME_AWAY'],
            'slug' => $match['AWAY_SLUG'],
            'logo' => $match['AWAY_IMAGES'][0] ?? null,
        ];

        // Update or create match entry in the 'calendario' table
        Calendario::updateOrCreate(
            ['match_id' => $match['EVENT_ID']],  // Use the unique match/event ID
            [
                'venue' => null,  // Venue data doesn't seem to be present in your response
                'matchday' => $match['ROUND'] ?? 'Unknown',
                'competition' => $tournament['TOURNAMENT_IMAGE'],  // Use the tournament image as competition reference
                'group' => $tournament['NAME_PART_2'],  // Example: Serie A
                'match_date' => $matchDate,  // Formatted match date
                'status' => $match['STAGE_TYPE'],
                'home_team' => json_encode($homeTeam),
                'away_team' => json_encode($awayTeam),
                'score' => json_encode([
                    'home' => $match['HOME_SCORE_CURRENT'] ?? 0,
                    'away' => $match['AWAY_SCORE_CURRENT'] ?? 0,
                ]),
                'goals' => null,  // The API response does not provide detailed goals
                'penalties' => null,  // The API response does not provide penalties
                'bookings' => null,  // The API response does not provide bookings
                'substitutions' => null,  // The API response does not provide substitutions
                'odds' => null,  // Odds can be filled if available
                'referees' => null,  // Referee information is missing in this response
            ]
        );
    }
}

        // Loop through the response data to get fixtures and match information


        // Return a success message
        return "Fixtures updated.";
    }

    }





}

