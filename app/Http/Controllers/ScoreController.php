<?php

namespace App\Http\Controllers;

use App\Models\Calendario;

class ScoreController extends Controller
{
    public function __invoke(Calendario $match)   // route-model binding
    {
        $score = json_decode($match->score, true);

        return response()->json([
            'home'        => $score['home'],
            'away'        => $score['away'],
            'updated_at'  => $match->updated_at->toDateTimeString(),
        ]);
    }
}