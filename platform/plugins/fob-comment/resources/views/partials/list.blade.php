@php
    $currentIndent ??= 0;

    if (! view()->exists($paginationView = Theme::getThemeNamespace('partials.pagination'))) {
        $paginationView = 'pagination::bootstrap-5';
    }
@endphp

<style>

    .fob-comment-item {
        margin-bottom: 12px;
    }

    .fob-comment-item .fob-comment-item-inner .fob-comment-item-avatar .fob-comment-item-img-container{
        border-radius: 50%;
        border: 1px solid #f0f0f0;
        padding: 4px;
    }

    .fob-comment-item .fob-comment-item-inner .fob-comment-item-avatar .fob-comment-item-img-container .fob-comment-item-img{
        width: 64px;
        height: 64px;
        display: flex;
    }

    .fob-comment-item .fob-comment-item-inner .fob-comment-item-avatar .fob-comment-item-img-container .fob-comment-item-img img{
        width: 100%;
        height: 100%;
    }
</style>

<div class="fob-comment-list">
    @foreach($comments as $comment)
        @continue(! $comment->is_approved && $comment->ip_address !== request()->ip())

        <div id="comment-{{ $comment->getKey() }}" class="fob-comment-item">
            <div class="fob-comment-item-inner">
                <div class="fob-comment-item-avatar">
                    @if ($comment->website)
                        <div class="fob-comment-item-img-container">
                            <a href="{{ $comment->website }}" class="fob-comment-item-img" target="_blank">
                                <img src="{{ $comment->avatar_url }}" alt="{{ $comment->name }}">
                            </a>
                        </div>
                    @else
                        <div class="fob-comment-item-img-container">
                            <div class="fob-comment-item-img">
                                <img src="{{ $comment->avatar_url }}" alt="{{ $comment->name }}">
                            </div>
                        </div>
                    @endif
                </div>
                <div class="fob-comment-item-content">
                    <div class="fob-comment-item-footer">
                        <div class="fob-comment-item-info bg-blue" >
                            <!-- Admin Badge -->

                            @if(\FriendsOfBotble\Comment\Support\CommentHelper::isDisplayAdminBadge() && $comment->is_admin)
                                <span class="fob-comment-item-admin-badge">
                                    {{ trans('plugins/fob-comment::comment.front.admin_badge') }}
                                </span>
                            @endif
                            @if ($comment->website)
                                <a href="{{ $comment->website }}" class="fob-comment-item-author" target="_blank">
                                    <h4 class="fob-comment-item-author">{{ $comment->name }}</h4>
                                </a>
                            @else
                                <h4 class="fob-comment-item-author">{{ $comment->name }}</h4>
                            @endif
                            <span class="fob-comment-item-date">{{ $comment->created_at->diffForHumans() }}</span>
                        </div>

                        @if ($comment->is_approved)
                            <a
                                href="{{ route('fob-comment.public.comments.reply', $comment) }}"
                                class="fob-comment-item-reply"

                                data-comment-id="{{ $comment->getKey() }}"
                                data-reply-to="{{ $replyLabel = trans('plugins/fob-comment::comment.front.list.reply_to', ['name' => $comment->name]) }}"
                                data-cancel-reply="{{ trans('plugins/fob-comment::comment.front.list.cancel_reply') }}"
                                aria-label="{{ $replyLabel }}"
                            ><i class="fa fa-reply" style="margin-right: 5px; font-size: 14px;"></i>
                                {{ trans('plugins/fob-comment::comment.front.list.reply') }}
                            </a>
                        @endif
                    </div>
                    <div class="fob-comment-item-body">
                        @if (! $comment->is_approved)
                            <em class="fob-comment-item-pending">
                                {{ trans('plugins/fob-comment::comment.front.list.waiting_for_approval_message') }}
                            </em>
                        @endif
                        @if($comment->is_admin)
                            {!! BaseHelper::clean($comment->formatted_content) !!}
                        @else
                            <p>{{ $comment->formatted_content }}</p>
                        @endif
                    </div>


                </div>
            </div>

            @if ($comment->replies->count())
                @include('plugins/fob-comment::partials.list', [
                    'comments' => $comment->replies,
                    'currentIndent' => $currentIndent + 1,
                ])
            @endif
        </div>
    @endforeach
</div>

@if ($comments instanceof \Illuminate\Contracts\Pagination\LengthAwarePaginator && $comments->hasPages())
    <div class="fob-comment-pagination">
        {{ $comments->appends(request()->except('page'))->links($paginationView) }}
    </div>
@endif
