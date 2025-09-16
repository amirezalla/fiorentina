<?php

namespace App\Http\Controllers\Member;

use App\Support\MemberActivity;
use Botble\Blog\Models\Post;
use FriendsOfBotble\Comment\Models\Comment;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;

class MemberActivityController extends Controller
{
    // Display all comments with pagination and filters
public function showComments(Request $request)
{
    $member = auth('member')->user();
    $perPage = $request->get('perPage', 10); // Default to 10 comments per page
    $sortBy = $request->get('sortBy', 'created_at_desc'); // Default sort by most recent
    $searchQuery = $request->get('search', ''); // Search query for comments and posts

    // Build the query for comments by this member
    $commentsQuery = Comment::query()
        ->where('author_id', $member->id)
        ->where('author_type', get_class($member))
        ->where('reference_type', Post::class);

    // Apply search if query exists
    if (!empty($searchQuery)) {
        $commentsQuery->where(function ($q) use ($searchQuery) {
            $q->where('content', 'like', '%' . $searchQuery . '%')
                ->orWhereHas('reference', function ($q) use ($searchQuery) {
                    $q->where('name', 'like', '%' . $searchQuery . '%');
                });
        });
    }

    // Apply sorting based on the selected sortBy option
    if ($sortBy == 'created_at_desc') {
        $commentsQuery->orderByDesc('created_at');
    } elseif ($sortBy == 'created_at_asc') {
        $commentsQuery->orderBy('created_at');
    } elseif ($sortBy == 'replies_count_desc') {
        // Fix the issue by using a subquery to count replies
        $commentsQuery->leftJoinSub(
            Comment::query()
                ->selectRaw('reference_id, count(*) as replies_count')
                ->whereNotNull('parent_id') // Ensure replies are counted by parent_id
                ->whereNull('deleted_at') // Ensure deleted comments are excluded
                ->groupBy('reference_id'),
            'replies_count_subquery',
            'fob_comments.reference_id',
            '=',
            'replies_count_subquery.reference_id'
        );
        $commentsQuery->orderByDesc('replies_count_subquery.replies_count');
    }

    // Paginate the results
    $commentsData = $commentsQuery->paginate($perPage);

    // Prepare comments data for the view (with replies count)
    $commentsData->transform(function ($comment) {
        $post = $comment->reference; // Get the related post
        $repliesCount = $comment->replies()->count(); // Get replies count

        return [
            'comment' => $comment,
            'post' => $post,
            'replies_count' => $repliesCount,
        ];
    });

    return view('member.activity.comments', compact('commentsData', 'searchQuery', 'sortBy'));
}





    // Display an individual comment and its replies
    public function show(Request $request, Comment $comment)
    {
        $member = Auth::guard('member')->user();
        abort_unless(($comment->user_id == $member->getKey() || $comment->customer_id == $member->getKey()), 403);

        $post = $comment->reference_type === Post::class ? Post::find($comment->reference_id) : null;

        // Get replies to the comment
        $replies = Comment::query()
            ->where('reference_type', $comment->reference_type)
            ->where('reference_id', $comment->reference_id)
            ->where('parent_id', $comment->id)
            ->where(function ($q) use ($member) {
                $q->where('user_id', '!=', $member->getKey())->orWhereNull('user_id');
            })
            ->orderBy('created_at', 'asc')
            ->paginate(30);

        // Prepare the view data
        $postLink = $post ? '<a href="'.e($post->url).'" target="_blank">'.e($post->name).'</a>' : '<em>(post removed)</em>';
        $orig = \BaseHelper::clean($comment->content);

        $items = '';
        foreach ($replies as $r) {
            $name = e($r->user_name ?? $r->author_name ?? __('User'));
            $when = e($r->created_at->diffForHumans());
            $body = \BaseHelper::clean($r->content);
            $items .= <<<HTML
            <div class="reply">
              <div class="meta">{$name} • {$when}</div>
              <div class="body">{$body}</div>
            </div>
            HTML;
        }

        $prev = $replies->previousPageUrl() ? '<a class="btn" href="'.e($replies->previousPageUrl()).'">« Prev</a>' : '';
        $next = $replies->nextPageUrl()     ? '<a class="btn" href="'.e($replies->nextPageUrl()).'">Next »</a>'     : '';
        $pager = ($prev || $next) ? '<div class="pager">'.$prev.' '.$next.'</div>' : '';

        $postAnchor = $post ? '<a class="small" href="'.e($post->url).'#comment-'.$comment->id.'" target="_blank">View on post</a>' : '';

        // Return HTML view
        $html = <<<HTML
        <!doctype html><html lang="en"><head>
          <meta charset="utf-8">
          <title>Replies to your comment</title>
          <style>
            body{font-family:system-ui,-apple-system,Segoe UI,Roboto,Arial,sans-serif;padding:24px;max-width:900px;margin:0 auto;}
            .card{border:1px solid #ddd;border-radius:10px;padding:16px;}
            .btn{display:inline-block;background:#4b2d7f;color:#fff;padding:8px 12px;border-radius:8px;text-decoration:none;margin-right:6px}
            .btn:visited{color:#fff}
            .meta{color:#666;font-size:12px;margin-bottom:6px}
            .body{white-space:pre-wrap;word-break:break-word}
            .reply{border:1px solid #eee;border-radius:10px;padding:12px;margin:10px 0}
            .pager{margin-top:12px}
          </style>
        </head><body>
          <div class="card">
            <h2>Replies to your comment</h2>
            <p><strong>Post:</strong> {$postLink}</p>
            <div class="meta">You • {$comment->created_at->diffForHumans()}</div>
            <div class="body">{$orig}</div>
            <div style="margin-top:6px;">{$postAnchor}</div>
            <hr>
            <h3>Replies</h3>
            {$items}
            {$pager}
          </div>
        </body></html>
        HTML;

        return response($html);
    }
}
