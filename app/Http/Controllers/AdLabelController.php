<?php

namespace App\Http\Controllers;

use App\Models\AdLabel;
use Illuminate\Http\Request;

class AdLabelController extends Controller
{
    public function suggest(Request $request)
    {
        $q = trim((string) $request->query('q', ''));
        if ($q === '') {
            // return the most recent 10 to start
            $items = AdLabel::query()->orderByDesc('id')->limit(10)->pluck('name');
        } else {
            $items = AdLabel::query()
                ->where('name', 'like', $q.'%')
                ->orderBy('name')
                ->limit(10)
                ->pluck('name');
        }

        return response()->json($items);
    }
}
