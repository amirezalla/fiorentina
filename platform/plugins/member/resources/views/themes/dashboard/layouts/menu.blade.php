


<ul class="menu">
    @foreach (DashboardMenu::getAll('member') as $item)
        @continue(! $item['name'] || $item['name'] === 'plugins/blog::member.posts')
        <li>
            <a
                href="{{ $item['url'] }}"
                @class(['active' => $item['active'] && $item['url'] !== BaseHelper::getHomepageUrl()])
            >
                <x-core::icon :name="$item['icon']" />
                {{ __($item['name']) }}
            </a>
        </li>
    @endforeach
</ul>


