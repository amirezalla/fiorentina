<?php

namespace App\Http\Controllers;

use App\Models\Poll;
use App\Models\PollOption;
use Illuminate\Http\Request;
use Botble\Base\Supports\Breadcrumb;
use Botble\Base\Http\Controllers\BaseController;

use Illuminate\Support\Facades\Response;

class PollController extends BaseController
{

    public function store(Request $request,$matchLineup)
    {
        dd($matchLineup,$request->user('customer'));
    }

}
