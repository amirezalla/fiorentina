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
use Illuminate\Support\Facades\Storage;


// sportmonks B0lZqWEdqBzEPrLW5gDcm87Svgb5bnEEa807fd7kOiONHbcbetXywqPQafqC

class DirettaController extends BaseController
{
    protected function breadcrumb(): Breadcrumb
    {
        return parent::breadcrumb()
            ->add("Diretta");
    }


    private function regenerateCommentaryFile($matchId)
{
    // 1) Load all non-deleted commentaries for the match
    $commentaries = MatchCommentary::where('match_id', $matchId)
        ->orderBy('id', 'desc')
        ->get()
        ->toArray();

    // 2) Create JSON
    $jsonContent = json_encode($commentaries);

    // 3) Build file path in Wasabi
    $filePath = "commentary/commentary_{$matchId}.json";

    // 4) Store in Wasabi (using the default disk or your wasabi disk)
    Storage::put($filePath, $jsonContent);

    // That’s it! The ETag changes in Wasabi, so the Node WebSocket server
    // will detect it.
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
        $newItem=MatchCommentary::create([
            'match_id' => $validatedData['match_id'],
            'comment_time' => $validatedData['time']."'",
            'comment_class' => $validatedData['tipo_event'],
            'comment_text' => $validatedData['comment_text'],
            'is_bold' => $request->has('is_bold'),
            'is_important' => $request->has('is_important'),
        ]);
        $commentaryData = $newItem->toArray(); 
        Queue::push(new StoreCommentaryJob($commentaryData));

    
        // Redirect back with a success message
        return redirect()->back()->with('success', 'Commentary added successfully.');
    }
    
    
    

    public function deleteCommentary(Request $request)
    {
        $commentaryId = $request->query('id');
        $commentary = MatchCommentary::find($commentaryId);
    
        if ($commentary) {
            $matchId = $commentary->match_id;
            $commentary->delete(); // Soft delete
    
            // Store the commentary ID in the session for undo
            Session::put('deleted_commentary_id', $commentaryId);
    
            // **Regenerate JSON** so Wasabi is in sync
            $this->regenerateCommentaryFile($matchId);
    
            return redirect()
                ->to("https://laviola.collaudo.biz/diretta/view?match_id=$matchId")
                ->with('success', 'Commentary deleted successfully. <a href="' . route('undo-commentary') . '">Undo</a>');
        }
    
        return redirect()->back()->with('error', 'Commentary not found');
    }
    

    public function undoCommentary()
    {
        $commentaryId = Session::get('deleted_commentary_id');
        if ($commentaryId) {
            // withTrashed() to find soft-deleted entries
            $commentary = MatchCommentary::withTrashed()->find($commentaryId);
            if ($commentary) {
                $matchId = $commentary->match_id;
                $commentary->restore();
                Session::forget('deleted_commentary_id');
    
                // **Regenerate JSON** after restoring
                $this->regenerateCommentaryFile($matchId);
    
                return redirect()->back()->with('success', 'Commentary restored successfully.');
            }
        }
    
        return redirect()->back()->with('error', 'Nothing to undo.');
    }
    

    public function updateCommentary(Request $request)
    {
        $validatedData = $request->validate([
            'id' => 'required|exists:match_commentaries,id',
            'comment_text' => 'required|string|max:500',
            'is_important' => 'nullable',
            'is_bold' => 'nullable',
        ]);
    
        $commentary = MatchCommentary::findOrFail($validatedData['id']);
    
        $commentary->is_important = $request->has('is_important') ? 1 : 0;
        $commentary->is_bold = $request->has('is_bold') ? 1 : 0;
        $commentary->comment_text = $validatedData['comment_text'];
        $commentary->save();
    
        // **Regenerate JSON** after update
        $this->regenerateCommentaryFile($commentary->match_id);
    
        return redirect()->back()->with('success', 'Commentary updated successfully.');
    }
    


    


}
    
