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
    public function show(Request $request, Comment $comment)
    {
        $member = Auth::guard('member')->user();
        abort_unless(($comment->user_id == $member->getKey() || $comment->customer_id == $member->getKey()), 403);

        $post = $comment->reference_type === Post::class ? Post::find($comment->reference_id) : null;

        $replies = \FriendsOfBotble\Comment\Models\Comment::query()
            ->where('reference_type', $comment->reference_type)
            ->where('reference_id', $comment->reference_id)
            ->where('parent_id', $comment->id)
            ->where(function ($q) use ($member) {
                $q->where('user_id', '!=', $member->getKey())->orWhereNull('user_id');
            })
            ->orderBy('created_at', 'asc')
            ->paginate(30);

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
