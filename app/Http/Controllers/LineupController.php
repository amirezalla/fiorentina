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

        $html = view('ads.includes.formazioni-tabs', [
                    'isHomeFiorentina' => $match->isHomeFiorentina(), // whatever helper you already have
                    'isAwayFiorentina' => $match->isAwayFiorentina(),
                    'fiorentinaLineups' => $fiorentina,
                    'anotherTeamLineups' => $another,
                ])->render();

        return response($html);
    }
}
