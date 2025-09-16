@extends('plugins/member::themes.dashboard.layouts.master')

@section('content')
    {!! apply_filters(MEMBER_TOP_STATISTIC_FILTER, null) !!}



    {{--    @if (is_plugin_active('blog')) --}}
    {{--        <x-core::stat-widget class="mb-3 row-cols-1 row-cols-sm-2 row-cols-md-3"> --}}
    {{--            <x-core::stat-widget.item --}}
    {{--                :label="trans('plugins/blog::member.published_posts')" --}}
    {{--                :value="$user->posts()->where('status', Botble\Base\Enums\BaseStatusEnum::PUBLISHED)->count()" --}}
    {{--                icon="ti ti-circle-check" --}}
    {{--                color="primary" --}}
    {{--            /> --}}

    {{--            <x-core::stat-widget.item --}}
    {{--                :label="trans('plugins/blog::member.pending_posts')" --}}
    {{--                :value="$user->posts()->where('status', Botble\Base\Enums\BaseStatusEnum::PENDING)->count()" --}}
    {{--                icon="ti ti-clock-hour-8" --}}
    {{--                color="success" --}}
    {{--            /> --}}

    {{--            <x-core::stat-widget.item --}}
    {{--                :label="trans('plugins/blog::member.draft_posts')" --}}
    {{--                :value="$user->posts()->where('status', Botble\Base\Enums\BaseStatusEnum::DRAFT)->count()" --}}
    {{--                icon="ti ti-notes" --}}
    {{--                color="danger" --}}
    {{--            /> --}}
    {{--        </x-core::stat-widget> --}}
    {{--    @endif --}}
    @php
        use App\Support\MemberActivity;
        use Illuminate\Support\Str;

        $member = auth('member')->user();
        $commentsData = $member ? MemberActivity::allCommentsWithReplies($member) : null;
    @endphp

    @if ($commentsData && $commentsData->isNotEmpty())
        @foreach ($commentsData as $data)
            @php
                $comment = $data['comment'];
                $post = $data['post'];
                $repliesCount = $data['replies_count'];
                $activityUrl = $comment
                    ? route('public.member.activity.comment', $comment->id)
                    : (Route::has('public.member.activity.comments')
                        ? route('public.member.activity.comments')
                        : null);
            @endphp

            <div class="card mb-3 activity-card">
                <div class="card-body py-3">
                    <div class="small text-muted mb-1">
                        {{ __('On') }}
                        @if ($post)
                            <a href="{{ $post->url }}" target="_blank" class="link-secondary">
                                {{ Str::limit($post->name, 80) }}
                            </a>
                        @else
                            <em>{{ __('(post removed)') }}</em>
                        @endif
                        • {{ $comment->created_at->diffForHumans() }}
                    </div>

                    <div class="mb-2 line-clamp-2 text-body">
                        {!! BaseHelper::clean(e(Str::limit(strip_tags($comment->content), 300))) !!}
                    </div>

                    <div class="d-flex align-items-center gap-3">
                        @if ($activityUrl)
                            <a href="{{ $activityUrl }}" class="small link-primary">
                                {{ __('View replies') }} @if ($repliesCount)
                                    ({{ $repliesCount }})
                                @endif
                            </a>
                        @endif

                        @if ($post)
                            <a href="{{ $post->url }}#comment-{{ $comment->id }}" target="_blank"
                                class="small link-secondary">
                                {{ __('Open in post') }}
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        @endforeach

        <div class="d-flex justify-content-between">
            <div>
                Showing {{ $commentsData->firstItem() }} to {{ $commentsData->lastItem() }} of
                {{ $commentsData->total() }} comments.
            </div>
            <div>
                {{ $commentsData->links() }}
            </div>
        </div>
    @else
        <p>{{ __('No comments found.') }}</p>
    @endif


    {{-- <activity-log-component></activity-log-component> --}}


@stop
<style>
    /* Card look to match the dashboard widgets */
    .activity-card {
        border: 1px solid #e9ecef;
        border-radius: .5rem;
    }

    /* Clean two-line clamp for the comment body */
    .line-clamp-2 {
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }

    .menu .activity-badge {
        margin-left: .5rem;
        background: #eee;
        color: #333;
        border-radius: 999px;
        padding: 0 .5rem;
        font-size: .75rem;
    }

    .menu-activity-preview {
        margin: .35rem 0 .5rem .5rem;
        padding: .5rem;
        border-left: 2px solid #eee;
    }

    .text-truncate-2 {
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
</style>
