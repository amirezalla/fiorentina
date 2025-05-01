<?php

namespace App\Http\Controllers;

use App\Models\Calendario;
use Illuminate\Support\Collection;
use App\Models\MatchLineups;


class LineupController extends Controller
{
    public function __invoke(Calendario $match)
    {
        /* ----------------------------------------------------------
         | 1. Pull rows and give the API column a nicer alias
         |-----------------------------------------------------------*/
        $lineups = MatchLineups::select('*', DB::raw('FORMATION_NAME as formation_name'))
                    ->where('match_id', $match->match_id)
                    ->get();

        /* ----------------------------------------------------------
         | 2. Group the collections the way Blade expects
         |    (case-insensitive compare to avoid surprises)
         |-----------------------------------------------------------*/
        $fiorentinaLineups = $lineups
            ->filter(fn ($l) =>
                in_array(strtolower($l->formation_name), [
                    'fiorentina subs',
                    'fiorentina coach',
                    'fiorentina initial lineup',
                ]))
            ->groupBy('formation_name');

        $anotherTeamLineups = $lineups
            ->filter(fn ($l) =>
                in_array(strtolower($l->formation_name), [
                    'another subs',
                    'another coach',
                    'another initial lineup',
                ]))
            ->groupBy('formation_name');

        /* ----------------------------------------------------------
         | 3. Same helper booleans you already use
         |-----------------------------------------------------------*/
        $home = json_decode($match->home_team, true);
        $away = json_decode($match->away_team, true);

        $isHomeFiorentina = strcasecmp($home['name'], 'Fiorentina') === 0;
        $isAwayFiorentina = strcasecmp($away['name'], 'Fiorentina') === 0;

        /* ----------------------------------------------------------
         | 4. Return the partial your JS swaps in
         |-----------------------------------------------------------*/
        return view('ads.includes.formazioni-tabs', [
            'isHomeFiorentina'   => $isHomeFiorentina,
            'isAwayFiorentina'   => $isAwayFiorentina,
            'fiorentinaLineups'  => $fiorentinaLineups,
            'anotherTeamLineups' => $anotherTeamLineups,
            'match'              => $match,
        ])->render();
    }
}
