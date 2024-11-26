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

    // Post a new comment
    public function storeCommentary(Request $request)
    {   
        $match_id=$request->input('match_id');
        $comment = DirettaComment::create([
            'match_id'=>$match_id,
            'event_type'=>$request->input('event_type'),
            'comment'=>$request->input('comments')??'',
            'details'=>$request->input('details')??'',
            'content' => $request->input('content'),
        ]);
        return response()->json($comment);
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
        'is_important' => 'nullable|boolean',
        'is_bold' => 'nullable|boolean',
    ]);

    // Find the commentary by ID
    $commentary = MatchCommentary::findOrFail($validatedData['id']);

    // Update the commentary fields
    $commentary->comment_text = $validatedData['comment_text'];
    $commentary->is_important = $validatedData['is_important'] ?? false;
    $commentary->is_bold = $validatedData['is_bold'] ?? false;
    $commentary->save();

    // Redirect back with a success message
    return redirect()->back()->with('success', 'Commentary updated successfully.');
}


    


}
    
