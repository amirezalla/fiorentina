<?php

namespace App\Support;

use Botble\Blog\Models\Post;
use FriendsOfBotble\Comment\Models\Comment;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;

class MemberActivity
{
    /**
     * Return latest comment & its post & up to $limit replies (from others).
     * @return array{comment:Comment, post:?Post, replies:Collection, replies_count:int}|null
     */
    public static function latestWithReplies($member, int $limit = 5): ?array
    {
        if (!$member) return null;

        $memberId   = $member->getKey();
        $memberType = get_class($member);

        // Latest comment by this member on a Post
        $comment = Comment::query()
            ->where('reference_type', Post::class)
            ->where('author_id', $memberId)
            ->where('author_type', $memberType)
            ->orderByDesc('created_at')
            ->first();

        if (!$comment) return null;

        $post = Post::find($comment->reference_id);

        // Replies from OTHERS to this specific comment
        $repliesBase = Comment::query()
            ->where('reference_type', $comment->reference_type)
            ->where('reference_id', $comment->reference_id)
            ->where('reply_to', $comment->id)
            ->where(function ($q) use ($memberId, $memberType) {
                $q->whereNull('author_id') // guests
                  ->orWhere('author_id', '!=', $memberId)
                  ->orWhereNull('author_type')
                  ->orWhere('author_type', '!=', $memberType);
            })
            ->orderBy('created_at', 'asc');

        return [
            'comment'       => $comment,
            'post'          => $post,
            'replies'       => (clone $repliesBase)->limit($limit)->get(),
            'replies_count' => (clone $repliesBase)->count(),
        ];
    }


    public static function allCommentsWithReplies($member, int $limit = 10): LengthAwarePaginator
{
    if (!$member) return collect([]);

    $memberId   = $member->getKey();
    $memberType = get_class($member);

    // Fetch all comments made by this member on a Post
    $comments = Comment::query()
        ->where('reference_type', Post::class)
        ->where('author_id', $memberId)
        ->where('author_type', $memberType)
        ->orderByDesc('created_at')
        ->paginate($limit);  // Pagination for comments

    $commentsData = [];
    foreach ($comments as $comment) {
        $post = Post::find($comment->reference_id);

        // Replies from OTHERS to this specific comment
        $repliesBase = Comment::query()
            ->where('reference_type', $comment->reference_type)
            ->where('reference_id', $comment->reference_id)
            ->where('reply_to', $comment->id)
            ->where(function ($q) use ($memberId, $memberType) {
                $q->whereNull('author_id') // guests
                  ->orWhere('author_id', '!=', $memberId)
                  ->orWhereNull('author_type')
                  ->orWhere('author_type', '!=', $memberType);
            })
            ->orderBy('created_at', 'asc');

        $commentsData[] = [
            'comment'       => $comment,
            'post'          => $post,
            'replies'       => $repliesBase->limit($limit)->get(),
            'replies_count' => $repliesBase->count(),
        ];
    }

    // Convert the commentsData array to a collection for easier handling
    return collect($commentsData);
}


    /**
     * Back-compat with blades calling latestForMember().
     * Same data as latestWithReplies but without the replies list.
     * @return array{comment:Comment, post:?Post, replies_count:int}|null
     */
    public static function latestForMember($member): ?array
    {
        $data = self::latestWithReplies($member, 0);
        if (!$data) return null;
        unset($data['replies']);
        return $data;
    }

    /** Optional: paginate all my comments on posts */
    public static function myCommentsPaginated(int $perPage = 20): LengthAwarePaginator
    {
        $member = Auth::guard('member')->user();
        $memberId   = $member->getKey();
        $memberType = get_class($member);

        return Comment::query()
            ->where('reference_type', Post::class)
            ->where('author_id', $memberId)
            ->where('author_type', $memberType)
            ->orderByDesc('created_at')
            ->paginate($perPage);
    }
}
