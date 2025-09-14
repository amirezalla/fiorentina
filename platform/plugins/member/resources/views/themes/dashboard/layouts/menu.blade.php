<ul class="menu">
    @foreach (DashboardMenu::getAll('member') as $item)
        @continue(!$item['name'] || $item['name'] === 'plugins/blog::member.posts')
        <li>
            <a href="{{ $item['url'] }}" @class([
                'active' =>
                    $item['active'] && $item['url'] !== BaseHelper::getHomepageUrl(),
            ])>
                <x-core::icon :name="$item['icon']" />
                {{ __($item['name']) }}
            </a>
        </li>
    @endforeach

    @php
        use App\Support\MemberActivity;
        $member = auth('member')->user();
        $act = $member ? MemberActivity::latestForMember($member) : null; // ['comment','post','replies_count']
        $c = $act['comment'] ?? null;
        $p = $act['post'] ?? null;
        $repliesCount = $act['replies_count'] ?? 0;
        $activityUrl = $c ? route('public.member.activity.comment', $c->id) : route('public.member.activity.comments');
    @endphp

    <li>
        <a href="{{ $activityUrl }}" @class(['active' => request()->routeIs('public.member.activity.*')])>
            <x-core::icon name="ti ti-message" />
            {{ __('Activity') }}
            @if ($repliesCount)
                <span class="badge activity-badge">{{ $repliesCount }}</span>
            @endif
        </a>

        @if ($c)
            <div class="menu-activity-preview">
                <div class="small text-muted">
                    {{ __('On') }}
                    @if ($p)
                        <a href="{{ $p->url }}"
                            target="_blank">{{ \Illuminate\Support\Str::limit($p->name, 40) }}</a>
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
                        • <a href="{{ $p->url }}#comment-{{ $c->id }}"
                            target="_blank">{{ __('Open in post') }}</a>
                    @endif
                </div>
            </div>
        @endif
    </li>
</ul>

<style>
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
