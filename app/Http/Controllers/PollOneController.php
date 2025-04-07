<?php

namespace App\Http\Controllers;

use App\Models\PollOne;
use App\Models\PollOption;
use Illuminate\Http\Request;
use Botble\Base\Supports\Breadcrumb;
use Botble\Base\Http\Controllers\BaseController;
use Illuminate\Support\Facades\Response;

class PollOneController extends BaseController
{
    protected function breadcrumb(): Breadcrumb
    {
        return parent::breadcrumb()->add("Polls");
    }

    public function index()
    {
        $this->pageTitle("Polls List");
        $polls = PollOne::with('options')->paginate(10);
        return view('polls.index', compact('polls'));
    }

    public function create()
    {
        $this->pageTitle("Crea");
        return view('polls.create');
    }

    public function storepoll(Request $request)
    {
        $request->validate([
            'question'     => 'required|string|max:255',
            'options.*'    => 'required|string|max:255',
            'min_choices'  => 'required|integer|min:1',
            'position'     => 'required|string|in:end,top,under_calendario',
            'expiry_date'  => 'nullable|date',
        ]);

        $poll = PollOne::create([
            'question'    => $request->question,
            'min_choices' => $request->min_choices,
            'position'    => $request->position,
            'expiry_date' => $request->expiry_date, // can be null or a valid date
        ]);

        foreach ($request->options as $option) {
            $poll->options()->create(['option' => $option]);
        }

        return redirect()->route('polls.index')->with('success', 'Sondaggio creato con successo!');
    }

    public function edit($id)
    {
        $this->pageTitle("Modifica Sondaggio");
        $poll = PollOne::with('options')->findOrFail($id);
        return view('polls.edit', compact('poll'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'question'     => 'required|string|max:255',
            'options.*'    => 'required|string|max:255',
            'min_choices'  => 'required|integer|min:1',
            'position'     => 'required|string|in:end,top,under_calendario',
            'expiry_date'  => 'nullable|date',
        ]);

        $poll = PollOne::findOrFail($id);
        $poll->update([
            'question'    => $request->question,
            'min_choices' => $request->min_choices,
            'position'    => $request->position,
            'expiry_date' => $request->expiry_date,
        ]);

        // Re-create poll options
        $poll->options()->delete();
        foreach ($request->options as $optionText) {
            $poll->options()->create(['option' => $optionText]);
        }

        return redirect()->route('polls.index')->with('success', 'Sondaggio aggiornato con successo!');
    }

    public function destroy($id)
    {
        PollOne::destroy($id);
        return redirect()->route('polls.index')->with('success', 'Sondaggio eliminato con successo');
    }

    public function toggleActive($id)
    {
        $poll = PollOne::findOrFail($id);
        $poll->active = !$poll->active;
        $poll->save();

        return redirect()->route('polls.index')->with('success', 'Stato del sondaggio aggiornato con successo');
    }

    public function exportResults($id)
    {
        $poll = PollOne::with('options')->findOrFail($id);
        $csvExporter = new \Laracsv\Export();
        $csv = $csvExporter->build($poll->options, ['option', 'votes'])->getCsv();

        return Response::make($csv, 200, [
            'Content-Type'        => 'application/csv',
            'Content-Disposition' => 'attachment; filename="poll_results.csv"',
        ]);
    }

    public function vote(Request $request)
    {
        // Retrieve the optionId from the JSON payload
        $optionId = $request->input('optionId');
        
        // Find the PollOption by its ID
        $option = PollOption::findOrFail($optionId);
        $poll=PollOne::findOrFail($option->poll_one_id);
    
        // Check if the poll is active
        if (!$poll->active) {
            return response()->json(['error' => 'Questo sondaggio è attualmente inattivo.'], 403);
        }
    
        // Increment the vote count for the option
        $option->votes=$option->votes+1;
        $option->save();
    
        // Return the updated poll results
        return redirect()->to(url()->current());
    }
    

    private function getResults($pollId)
    {
        $poll = PollOne::findOrFail($pollId);
        $options = PollOption::where('poll_id', $pollId)->get();
        $totalVotes = $options->sum('votes');

        $results = $options->map(function ($option) use ($totalVotes) {
            return [
                'id'         => $option->id,
                'option'     => $option->option,
                'votes'      => $option->votes,
                'percentage' => $totalVotes > 0
                    ? round(($option->votes / $totalVotes) * 100, 2)
                    : 0
            ];
        });

        return ['results' => $results];
    }
}
