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

    .fob-comment-item .fob-comment-item-inner {
        position: relative;
    }

    .fob-comment-item .fob-comment-item-inner .fob-comment-item-img-container {
        border: 1px solid #f0f0f0;
        border-radius: 50%;
        padding: 4px;
        position: absolute;
        left: 0;
        top: 0;
    }

    .fob-comment-item .fob-comment-item-inner .fob-comment-item-img-container .fob-comment-item-img {
        width: 60px;
        height: 60px;
        display: flex;
        border-radius: 50%;
        overflow: hidden;
    }

    .fob-comment-item .fob-comment-item-inner .fob-comment-item-img-container .fob-comment-item-img img {
        width: 100%;
        height: 100%;
    }

    .fob-comment-item .fob-comment-item-content {
        padding-left: 34px;
    }

    .fob-comment-item .fob-comment-item-content .fob-comment-item-footer{
        width: 100%;
        display: block;
    }

    .fob-comment-item .fob-comment-item-content .fob-comment-item-info {
        width: 100%;
        padding-left: 42px;
        background-color: #fafafa;
    }

    .fob-comment-item .fob-comment-item-content .fob-comment-item-info .fob-comment-item-author{
        width: 100%;
        font-size: 18px;
        padding-top: 5px;
        padding-bottom: 5px;
        color: var(--bs-purple);
        border-bottom: 1px dashed var(--bs-purple);
    }

    .fob-comment-item .fob-comment-item-content .fob-comment-item-date {
        width: 100%;
        padding-left: 42px;
    }

    .fob-comment-item .fob-comment-item-content .fob-comment-item-content-inside{
        padding-left: 42px;
    }
</style>

<div class="fob-comment-list">
    @foreach($comments as $comment)
        @continue(! $comment->is_approved && $comment->ip_address !== request()->ip())

        <div id="comment-{{ $comment->getKey() }}" class="fob-comment-item">
            <div class="fob-comment-item-inner">
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
                <div class="fob-comment-item-content">
                    <div class="fob-comment-item-footer">
                        <div class="fob-comment-item-info">
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
                        </div>
                        <span class="fob-comment-item-date">{{ $comment->created_at->diffForHumans() }}</span>
                    </div>
                    <div class="fob-comment-item-content-inside">
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
