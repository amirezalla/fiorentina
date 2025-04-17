<?php

namespace App\Http\Controllers;

use App\Models\MatchCommentary;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Queue;
use Botble\Base\Supports\Breadcrumb;
use Botble\Base\Http\Controllers\BaseController;
use Illuminate\Validation\ValidationException;

class DirettaController extends BaseController
{
    /* ------------------------------------------------------------------
     | Breadcrumb for admin pages
     * ---------------------------------------------------------------- */
    protected function breadcrumb(): Breadcrumb
    {
        return parent::breadcrumb()->add('Diretta');
    }

    /* ------------------------------------------------------------------
     | Page that shows the commentary feed
     * ---------------------------------------------------------------- */
    public function view()
    {
        $matchId = request()->query('match_id');
        $this->pageTitle("Diretta {$matchId}");
        return view('diretta.view', compact('matchId'));
    }

    /* ------------------------------------------------------------------
     | Page that shows the chat (same template, other tab)
     * ---------------------------------------------------------------- */
    public function chatView()
    {
        $matchId = request()->query('match_id');
        $this->pageTitle("Chat {$matchId}");
        return view('diretta.view-chat', compact('matchId'));
    }

    /* ------------------------------------------------------------------
     | ==========  ADMIN  : add commentary  =============================
     * ---------------------------------------------------------------- */
    public function storeCommentary(Request $request)
    {
        $data = $request->validate([
            'match_id'      => 'required',
            'time'          => 'nullable|string|max:10',
            'tipo_event'    => 'required|string|max:255',
            'comment_text'  => 'required|string|max:500',
            'is_bold'       => 'nullable|boolean',
            'is_important'  => 'nullable|boolean',
        ]);

        $item = MatchCommentary::create([
            'match_id'      => $data['match_id'],
            'comment_time'  => $data['time'] ? "{$data['time']}'" : null,
            'comment_class' => $data['tipo_event'],
            'comment_text'  => $data['comment_text'],
            'is_bold'       => (bool) ($data['is_bold'] ?? 0),
            'is_important'  => (bool) ($data['is_important'] ?? 0),
        ]);

        // update JSON once
        $this->regenerateCommentaryFile($item->match_id);

        return back()->with('success', 'Commentary added.');
    }

    /* ------------------------------------------------------------------
     | ==========  ADMIN  : AJAX update  ================================
     * route: PATCH /commentary/{id}
     * ---------------------------------------------------------------- */
    public function ajaxUpdate(Request $request)
    {

        $id=$request->id;

        $c = MatchCommentary::findOrFail($id);

        $c->update([
            'comment_text'  => $request->comment_text,
            'is_bold'       => $request->boolean('is_bold'),
            'is_important'  => $request->boolean('is_important'),
        ]);

        $this->regenerateCommentaryFile($c->match_id);

        return response()->json(['success' => true]);
    }

    /* ------------------------------------------------------------------
     | ==========  ADMIN  : AJAX soft‑delete  ===========================
     * route: DELETE /commentary/{id}
     * ---------------------------------------------------------------- */
    public function ajaxDelete(Request $request)
    {
        $id=$request->id;
        $c = MatchCommentary::findOrFail($id);
        $c->delete();                                 // soft delete
        $this->regenerateCommentaryFile($c->match_id);
        return response()->json(['success' => true]);
    }

    /* ------------------------------------------------------------------
     | ==========  ADMIN  : AJAX restore  ==============================
     * route: POST /commentary/{id}/restore
     * ---------------------------------------------------------------- */
    public function ajaxRestore(Request $request)
    {
        $id=$request->id;
        $c = MatchCommentary::withTrashed()->findOrFail($id);
        $c->restore();
        $this->regenerateCommentaryFile($c->match_id);
        return response()->json(['success' => true]);
    }

    /* ------------------------------------------------------------------
     | Helper: rewrite Wasabi JSON (non‑deleted rows only)
     * ---------------------------------------------------------------- */
    private function regenerateCommentaryFile($matchId): void
    {
        $commentaries = MatchCommentary::where('match_id', $matchId)
                        ->orderBy('id', 'desc')
                        ->get();                         // SoftDeletes hides trashed

        Storage::put(
            "commentary/commentary_{$matchId}.json",
            $commentaries->toJson()
        );
    }
}
