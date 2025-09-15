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
