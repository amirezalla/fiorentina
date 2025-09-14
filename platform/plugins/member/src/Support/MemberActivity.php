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

        $comment = Comment::query()
            ->where(function ($q) use ($memberId) {
                $q->where('user_id', $memberId)->orWhere('customer_id', $memberId);
            })
            ->where(function ($q) use ($memberType) {
                $q->where('user_type', $memberType)->orWhereNull('user_type');
            })
            ->where('reference_type', Post::class)
            ->orderByDesc('created_at')
            ->first();

        if (!$comment) return null;

        $post = Post::find($comment->reference_id);

        $repliesBase = Comment::query()
            ->where('reference_type', $comment->reference_type)
            ->where('reference_id', $comment->reference_id)
            ->where('parent_id', $comment->id)
            ->where(function ($q) use ($memberId) {
                $q->where('user_id', '!=', $memberId)->orWhereNull('user_id');
            })
            ->orderBy('created_at', 'asc');

        return [
            'comment'       => $comment,
            'post'          => $post,
            'replies'       => $repliesBase->clone()->limit($limit)->get(),
            'replies_count' => (clone $repliesBase)->count(),
        ];
    }

    /** Optional: paginate all my comments */
    public static function myCommentsPaginated(int $perPage = 20): LengthAwarePaginator
    {
        $member = Auth::guard('member')->user();
        $memberId = $member->getKey();
        $memberType = get_class($member);

        return Comment::query()
            ->where(function ($q) use ($memberId) {
                $q->where('user_id', $memberId)->orWhere('customer_id', $memberId);
            })
            ->where(function ($q) use ($memberType) {
                $q->where('user_type', $memberType)->orWhereNull('user_type');
            })
            ->where('reference_type', Post::class)
            ->orderByDesc('created_at')
            ->paginate($perPage);
    }
}
