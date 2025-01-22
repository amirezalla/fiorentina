<?php

namespace FriendsOfBotble\Comment\Http\Controllers\Fronts;

use Botble\Base\Http\Controllers\BaseController;
use Botble\Base\Models\BaseModel;
use FriendsOfBotble\Comment\Actions\CreateNewComment;
use FriendsOfBotble\Comment\Actions\GetCommentReference;
use FriendsOfBotble\Comment\Enums\CommentStatus;
use FriendsOfBotble\Comment\Http\Requests\Fronts\CommentReferenceRequest;
use FriendsOfBotble\Comment\Http\Requests\Fronts\CommentRequest;
use FriendsOfBotble\Comment\Models\Comment;
use FriendsOfBotble\Comment\Support\CommentHelper;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CommentController extends BaseController
{
    public function index(CommentReferenceRequest $request, GetCommentReference $getCommentReference)
    {
        $reference = new BaseModel();

        if ($request->input('reference_type')) {
            $reference = $getCommentReference($request->input('reference_type'), $request->input('reference_id'));

            $query = Comment::query()
                ->where('reference_id', $reference->getKey())
                ->where('reference_type', $reference::class);
        } else {
            $query = Comment::query()
                ->where('reference_url', $request->input('reference_url'));
        }

        $query
            ->withCount(['likes', 'dislikes', 'replies'])
            ->where(function (Builder $query) {
                $query
                    ->where('status', CommentStatus::APPROVED)
                    ->orWhere(function (Builder $query) {
                        $query->where('status', CommentStatus::PENDING)
                            ->where('ip_address', request()->ip());
                    });
            })
            ->where('reply_to', null)
            ->with(['replies']);
        $query->orderByDesc('created_at');
        /*if ($request->filled('sort') && $request->get('sort') == "must-reaction") {
            $query->orderByDesc('likes_count');
        } else if ($request->filled('sort') && $request->get('sort') == "must-replies") {
            $query->orderByDesc('replies_count');
        }
        if ($request->filled('sort2') && $request->get('sort2') == "latest") {
            $query->orderByDesc('created_at');
        } elseif ($request->filled('sort2') && $request->get('sort2') == "oldest") {
            $query->orderBy('created_at');
        } else {
            $query->orderBy('created_at', CommentHelper::getCommentOrder());
        }*/
        $comments = apply_filters('fob_comment_list_query', $query, $request)->paginate(10);
        $count = CommentHelper::getCommentsCount($reference);

        $view = apply_filters('fob_comment_list_view_path', 'plugins/fob-comment::partials.list');

        return $this
            ->httpResponse()
            ->setData([
                'title' => trans_choice('plugins/fob-comment::comment.front.list.title', $count, ['count' => $count]),
                'html' => view($view, compact('comments'))->render(),
                'comments' => $comments,
            ]);
    }

    public function store(
        CommentRequest      $request,
        CreateNewComment    $createNewComment,
        GetCommentReference $getCommentReference
    )
    {
        $data = [
            ...$request->validated(),
            'reference_url' => $request->input('reference_url') ?? url()->previous(),
        ];

        $reference = new BaseModel();

        if ($request->input('reference_type')) {
            $reference = $getCommentReference($request->input('reference_type'), $request->input('reference_id'));

            if ($reference->getMetaData('allow_comments', true) == '0') {
                abort(404);
            }
        }

        $createNewComment($reference, $data);

        return $this
            ->httpResponse()
            ->setMessage(trans('plugins/fob-comment::comment.front.comment_success_message'));
    }

    /**
     * @param Request $request
     * @param $comment
     * @return JsonResponse
     */
    public function like(Request $request, $comment)
    {
        $comment = Comment::query()
            ->where('status', CommentStatus::APPROVED)
            ->findOrFail($comment);
        if (is_null($request->user())) {
            return response()->json([
                'message' => "Unauthenticated.",
            ], Response::HTTP_UNAUTHORIZED);
        }
        if ($comment->likes->contains($request->user()->id)) {
            $comment->likes()->detach($request->user()->id);
        } else {
            $comment->likes()->attach($request->user()->id);
        }
        return response()->json([
            'message' => "Success",
            'count' => number_format($comment->likes()->count()),
        ]);
    }

    /**
     * @param Request $request
     * @param $comment
     * @return JsonResponse
     */
    public function dislike(Request $request, $comment)
    {
        $comment = Comment::query()
            ->where('status', CommentStatus::APPROVED)
            ->findOrFail($comment);
        if (is_null($request->user())) {
            return response()->json([
                'message' => "Unauthenticated.",
            ], Response::HTTP_UNAUTHORIZED);
        }
        if ($comment->dislikes->contains($request->user()->id)) {
            $comment->dislikes()->detach($request->user()->id);
        } else {
            $comment->dislikes()->attach($request->user()->id);
        }
        return response()->json([
            'message' => "Success",
            'count' => number_format($comment->dislikes()->count()),
        ]);
    }
}
