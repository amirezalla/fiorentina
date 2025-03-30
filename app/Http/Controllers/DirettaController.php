<?php


namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Standing;
use App\Models\Matches;
use App\Models\Calendario;
use App\Models\DirettaComment;
use App\Models\MatchCommentary;
use Illuminate\Support\Facades\Http;
use Carbon\Carbon;
use Illuminate\Support\Facades\Session;

use Botble\Base\Supports\Breadcrumb;

use Botble\Base\Http\Controllers\BaseController;
use App\Jobs\StoreCommentaryJob;
use Illuminate\Support\Facades\Queue;

// sportmonks B0lZqWEdqBzEPrLW5gDcm87Svgb5bnEEa807fd7kOiONHbcbetXywqPQafqC

class DirettaController extends BaseController
{
    protected function breadcrumb(): Breadcrumb
    {
        return parent::breadcrumb()
            ->add("Diretta");
    }
    // Fetch latest comments
    public function fetchLastComments(Request $request)
    {
        $match_id=$request->input('match_id');
        $comments = DirettaComment::where('match_id', $lastId)->orderBy('created_at', 'asc')->get();
        return response()->json($comments);
    }

    public function view(){
        $matchId = request()->query('match_id');
    
        $this->pageTitle("Diretta di $matchId");

        return view('diretta.view',compact('matchId'));
    }

    public function chatView(){
        $matchId = request()->query('match_id');
    
        $this->pageTitle("Chat di $matchId");

        return view('diretta.view-chat',compact('matchId'));
    }

    public function storeCommentary(Request $request)
    {
        // Validate the incoming request data
        $validatedData = $request->validate([
            'match_id' => 'required',
            'time' => 'required',
            'tipo_event' => 'required|string|max:255',
            'comment_text' => 'required|string|max:500',
            'is_bold' => 'nullable|boolean',
            'is_important' => 'nullable|boolean',
        ]);
    
        // Create a new commentary
        MatchCommentary::create([
            'match_id' => $validatedData['match_id'],
            'comment_time' => $validatedData['time']."'",
            'comment_class' => $validatedData['tipo_event'],
            'comment_text' => $validatedData['comment_text'],
            'is_bold' => $request->has('is_bold'),
            'is_important' => $request->has('is_important'),
        ]);
        Queue::push(new StoreCommentaryJob($commentaryData));

    
        // Redirect back with a success message
        return redirect()->back()->with('success', 'Commentary added successfully.');
    }
    
    
    

    public function deleteCommentary(Request $request)
{
    // Get the commentary ID from the request
    $commentaryId = $request->query('id');

    // Fetch the commentary by its ID
    $commentary = MatchCommentary::find($commentaryId);

    // If the commentary exists, soft delete it
    if ($commentary) {
        $matchId = $commentary->match_id; // Get the match ID before deletion
        $commentary->delete(); // Soft delete the commentary

        // Store the commentary ID in the session for undo functionality
        Session::put('deleted_commentary_id', $commentaryId);

        // Redirect with a success message and an option to undo
        return redirect()->to("https://laviola.collaudo.biz/diretta/view?match_id=$matchId")
                         ->with('success', 'Commentary deleted successfully. <a href="' . route('undo-commentary') . '">Undo</a>');
    }

    // If the commentary doesn't exist, handle it (optional)
    return redirect()->back()->with('error', 'Commentary not found');
}

public function undoCommentary()
{
    // Check if there's a deleted commentary ID in the session
    $commentaryId = Session::get('deleted_commentary_id');

    if ($commentaryId) {
        // Fetch the soft-deleted commentary
        $commentary = MatchCommentary::withTrashed()->find($commentaryId);

        // Restore the commentary if it was soft deleted
        if ($commentary) {
            $commentary->restore();

            // Clear the session after restoring
            Session::forget('deleted_commentary_id');

            // Redirect back with a success message
            return redirect()->back()->with('success', 'Commentary restored successfully.');
        }
    }

    // If there's no commentary to restore
    return redirect()->back()->with('error', 'Nothing to undo.');
}

public function updateCommentary(Request $request)
{
    // Validate the incoming request data
    $validatedData = $request->validate([
        'id' => 'required|exists:match_commentaries,id',
        'comment_text' => 'required|string|max:500',
        'is_important' => 'nullable',
        'is_bold' => 'nullable',
    ]);

    // Find the commentary by ID
    $commentary = MatchCommentary::findOrFail($validatedData['id']);

    // Normalize the values for 'is_important' and 'is_bold' to ensure true or false
    $commentary->is_important = $request->has('is_important') ? 1 : 0;
    $commentary->is_bold = $request->has('is_bold') ? 1 : 0;

    // Update the comment text
    $commentary->comment_text = $validatedData['comment_text'];
    $commentary->save();

    // Redirect back with a success message
    return redirect()->back()->with('success', 'Commentary updated successfully.');
}


    


}
    
