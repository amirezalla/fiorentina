<?php

namespace App\Http\Controllers;

use App\Models\MatchLineups;
use App\Models\Poll;
use App\Models\PollOption;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Botble\Base\Supports\Breadcrumb;
use Botble\Base\Http\Controllers\BaseController;

use Illuminate\Support\Facades\Response;

class PollController extends BaseController
{

    /**
     * @param Request $request
     * @param $matchLineup
     * @return JsonResponse
     */
    public function store(Request $request, $matchLineup)
    {
        $member = $request->user('member');
        $matchLineup = MatchLineups::find($matchLineup);
        if (is_null($member)) {
            return Response::json([
                'success' => false,
            ], 403);
        }
        if (is_null($matchLineup)) {
            return Response::json([
                'success' => false,
            ], 404);
        }
        $matchLineup->polls()->updateOrCreate([
            'member_id' => $member->id,
        ], [
            'member_id' => $member->id,
            'value' => $request->rate,
        ]);
        return Response::json([
            'success' => true,
            'value' => $request->rate
        ]);
    }

}
