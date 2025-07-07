<ul @class(['navbar-nav', $navbarClass ?? null])>

    @foreach (DashboardMenu::getAll() as $menu)
        @if (
            $menu['id'] == 'cms-core-plugins' ||
                $menu['id'] == 'cms-plugins-custom-field' ||
                $menu['id'] == 'cms-plugins-block' ||
                $menu['id'] == 'cms-plugins-gallery')
        @else
            @include('core/base::layouts.partials.navbar-nav-item', [
                'menu' => $menu,
                'autoClose' => $autoClose,
                'isNav' => true,
            ])
        @endif
    @endforeach

    <li class="nav-item dropdown">
        <a href="{{ route('videos.index') }}" class="nav-link dropdown-toggle nav-priority-3000" id="ads"
            data-bs-auto-close="false" role="button" aria-expanded="false" title="Ads">
            <span class="nav-link-icon d-md-none d-lg-inline-block"><i class="fa fa-link"></i></span>
            <span class="nav-link-title  text-truncate">Videos</span>
        </a>
    </li>
    <li class="nav-item dropdown">
        <a href="#" class="nav-link dropdown-toggle nav-priority-3000" id="adsDropdown" role="button"
            data-bs-toggle="dropdown" data-bs-auto-close="outside" aria-expanded="false" title="Ads">
            <span class="nav-link-icon d-md-none d-lg-inline-block"><i class="fa fa-link"></i></span>
            <span class="nav-link-title text-truncate">Ads</span>
        </a>
        <ul class="dropdown-menu" aria-labelledby="adsDropdown">
            <li>
                <a class="dropdown-item" href="{{ route('ads.create') }}" aria-label="Create Ad">
                    Create
                </a>
            </li>
            <li>
                <a class="dropdown-item" href="{{ route('ads.index') }}" aria-label="List Ads">
                    List
                </a>
            </li>
            <li>
                <a class="dropdown-item" href="{{ route('ads.groups.index') }}" aria-label="Groups">
                    Groups
                </a>
            </li>
        </ul>
    </li>

    <li class="nav-item dropdown">
        <a href="{{ route('players.index') }}" class="nav-link dropdown-toggle nav-priority-3000" id="ads"
            data-bs-auto-close="false" role="button" aria-expanded="false" title="Ads">
            <span class="nav-link-icon d-md-none d-lg-inline-block"><i class="fa fa-link"></i></span>
            <span class="nav-link-title  text-truncate">I Giocatori</span>
        </a>
    </li>
    <li class="nav-item dropdown">
        <a class="nav-link dropdown-toggle nav-priority-2000" href="#polls" id="polls" data-bs-toggle="dropdown"
            data-bs-auto-close="false" role="button" aria-expanded="false" title="Aspetto">
            <span class="nav-link-icon d-md-none d-lg-inline-block"><i class="fa fa-poll"></i></span>
            <span class="nav-link-title text-truncate">
                Polls
            </span>
        </a>
        <div class="dropdown-menu animate slideIn dropdown-menu-start">
            <a href="{{ route('polls.create') }}" class="dropdown-item nav-priority-1">
                <span class="nav-link-title text-truncate">
                    Crea
                </span>
            </a>
            <a href="{{ route('polls.index') }}" class="dropdown-item nav-priority-2">

                <span class="nav-link-title text-truncate">
                    View
                </span>
            </a>
        </div>
    </li>

    <li class="nav-item dropdown">
        <a href="{{ route('diretta.list') }}" class="nav-link dropdown-toggle nav-priority-3000" id="chat"
            data-bs-auto-close="false" role="button" aria-expanded="false" title="chat">
            <span class="nav-link-icon d-md-none d-lg-inline-block"><i class="fa fa-tv"></i></span>
            <span class="nav-link-title  text-truncate">Gestione delle dirette</span>
        </a>
    </li>


    <li class="nav-item ">
        <a href="{{ route('chat-settings.index') }}" class="nav-link  nav-priority-3000" id="chat-settings"
            data-bs-auto-close="false" role="button" aria-expanded="false" title="chat">
            <span class="nav-link-icon d-md-none d-lg-inline-block"><i class="fa fa-message"></i></span>
            <span class="nav-link-title  text-truncate">Impostazioni della chat</span>
        </a>
    </li>

    <li class="nav-item ">
        <a href="/admin/yt-widget" class="nav-link  nav-priority-3000" id="chat-settings" data-bs-auto-close="false"
            role="button" aria-expanded="false" title="chat">
            <span class="nav-link-icon d-md-none d-lg-inline-block"><i class="fa fa-youtube"></i></span>
            <span class="nav-link-title  text-truncate">YT Widget</span>
        </a>
    </li>

</ul>
