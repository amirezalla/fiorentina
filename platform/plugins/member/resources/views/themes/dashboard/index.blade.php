@extends('plugins/member::themes.dashboard.layouts.master')

@section('content')
    {!! apply_filters(MEMBER_TOP_STATISTIC_FILTER, null) !!}

    @php
        use App\Support\MemberActivity;
        $member = auth('member')->user();
        $act = $member ? MemberActivity::latestForMember($member) : null; // ['comment','post','replies_count']
        $c = $act['comment'] ?? null;
        $p = $act['post'] ?? null;
        $repliesCount = $act['replies_count'] ?? 0;
        $activityUrl = $c ? route('public.member.activity.comment', $c->id) : route('public.member.activity.comments');
    @endphp
    @if ($c)
        <div class="menu-activity-preview">
            <div class="small text-muted">
                {{ __('On') }}
                @if ($p)
                    <a href="{{ $p->url }}" target="_blank">{{ \Illuminate\Support\Str::limit($p->name, 40) }}</a>
                @else
                    <em>{{ __('(post removed)') }}</em>
                @endif
                • {{ $c->created_at->diffForHumans() }}
            </div>

            <div class="text-truncate-2">
                {!! BaseHelper::clean(e(\Illuminate\Support\Str::limit(strip_tags($c->content), 120))) !!}
            </div>

            <div class="small mt-1">
                <a href="{{ $activityUrl }}">{{ __('View replies') }}</a>
                @if ($p)
                    • <a href="{{ $p->url }}#comment-{{ $c->id }}" target="_blank">{{ __('Open in post') }}</a>
                @endif
            </div>
        </div>
    @endif

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

    <activity-log-component></activity-log-component>
@stop
