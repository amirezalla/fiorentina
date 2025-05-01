<?php

namespace App\Http\Controllers;

use App\Models\Calendario;
use Illuminate\Support\Collection;

class LineupController extends Controller
{
    public function __invoke(Calendario $match)
    {
        $raw   = json_decode($match->lineups ?? '[]');
        $all   = collect($raw)->groupBy('FORMATION_NAME');

        $fiorentinaLineups = $lineups
        ->filter(fn ($l) => in_array($l->formation_name, [
            'Fiorentina Subs', 'Fiorentina Coach', 'Fiorentina Initial Lineup',
        ]))
        ->groupBy('formation_name');
    
    $anotherTeamLineups = $lineups
        ->filter(fn ($l) => in_array($l->formation_name, [
            'Another Subs', 'Another Coach', 'Another Initial Lineup',
        ]))
        ->groupBy('formation_name');

        $home = json_decode($match->home_team, true);   // ['name', 'id', 'slug', ...]
$away = json_decode($match->away_team, true);




$isHomeFiorentina = strcasecmp($home['name'], 'Fiorentina') === 0;
$isAwayFiorentina = strcasecmp($away['name'], 'Fiorentina') === 0;

return view('ads.includes.formazioni-tabs', [
    'isHomeFiorentina'   => $isHomeFiorentina,
    'isAwayFiorentina'   => $isAwayFiorentina,
    'fiorentinaLineups'  => $fiorentinaLineups,
    'anotherTeamLineups' => $anotherTeamLineups,
]);

    }
}
