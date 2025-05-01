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

        // the same grouping logic you have in Blade
        $fiorentina = $all->only([
            'Fiorentina Subs',
            'Fiorentina Coach',
            'Fiorentina Initial Lineup',
        ]);

        $another    = $all->only([
            'Another Subs',
            'Another Coach',
            'Another Initial Lineup',
        ]);

        $home = json_decode($match->home_team, true);   // ['name', 'id', 'slug', ...]
$away = json_decode($match->away_team, true);

$isHomeFiorentina = strcasecmp($home['name'], 'Fiorentina') === 0;
$isAwayFiorentina = strcasecmp($away['name'], 'Fiorentina') === 0;

return view('ads.includes.formazioni-tabs', [
    'isHomeFiorentina'   => $isHomeFiorentina,
    'isAwayFiorentina'   => $isAwayFiorentina,
    'fiorentinaLineups'  => $fiorentina,
    'anotherTeamLineups' => $another,
]);


        return response($html);
    }
}
