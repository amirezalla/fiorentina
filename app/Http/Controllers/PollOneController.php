<?php

namespace App\Http\Controllers;

use App\Models\MatchLineups;
use App\Models\Poll;
use App\Models\PollOne;
use App\Models\PollOption;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Botble\Base\Supports\Breadcrumb;
use Botble\Base\Http\Controllers\BaseController;

use Illuminate\Support\Facades\Response;

class PollController extends BaseController
{


    protected function breadcrumb(): Breadcrumb
    {
        return parent::breadcrumb()
            ->add("Polls");
    }
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
            'value' => $request->rate,
            'rate_info'=>$matchLineup->getRateInfo(),
        ]);
    }


    public function create()
    {
        $this->pageTitle("Crea");

        return view('polls.create');
    }

    public function index()
    {
        $this->pageTitle("Polls List");

        $polls = PollOne::with('options')->paginate(10);
        return view('polls.index', compact('polls'));
    }

    public function toggleActive($id)
    {
        $poll = Poll::findOrFail($id);
        $poll->active = !$poll->active;
        $poll->save();

        return redirect()->route('polls.index')->with('success', 'Poll status changed successfully');
    }

    public function destroy($id)
    {
        $poll = PollOne::findOrFail($id);
        $poll->delete();

        return redirect()->route('polls.index')->with('success', 'Poll deleted successfully');
    }

    public function storepoll(Request $request)
    {
        $request->validate([
            'question' => 'required|string|max:255',
            'options.*' => 'required|string|max:255',
        ]);

        $poll = new PollOne(['question' => $request->question]);
        $poll->save();

        foreach ($request->options as $option) {
            $poll->options()->create(['option' => $option]);
        }

        return redirect()->route('polls.index')->with('success', 'Poll created successfully!');
    }




    public function exportResults($id)
    {
        $poll = PollOne::with('options')->findOrFail($id);
        $csvExporter = new \Laracsv\Export();
        $csv = $csvExporter->build($poll->options, ['option', 'votes'])->getCsv();

        return Response::make($csv, 200, [
            'Content-Type' => 'application/csv',
            'Content-Disposition' => 'attachment; filename="poll_results.csv"',
        ]);
    }

    public function vote(Request $request, $optionId)
    {
        $option = PollOption::findOrFail($optionId);
        if (!$option->poll->active) {
            return response()->json(['error' => 'This poll is currently inactive.'], 403);
        }
        $option->votes += 1;
        $option->save();

        $results = $this->getResults($option->poll_id);

        return response()->json($results);
    }

    private function getResults($pollId)
    {
        $poll = PollOne::with('options')->findOrFail($pollId);
        $totalVotes = $poll->options->sum('votes');
        $results = $poll->options->map(function ($option) use ($totalVotes) {
            return [
                'id' => $option->id,
                'option' => $option->option,
                'votes' => $option->votes,
                'percentage' => $totalVotes > 0 ? round(($option->votes / $totalVotes) * 100, 2) : 0
            ];
        });

        return ['results' => $results];
    }










}
