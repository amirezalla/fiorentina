@php
    use App\Models\PollOne;
    use Carbon\Carbon;

    use Botble\Blog\Models\Post;
    use Illuminate\Support\Facades\DB;
    $poll = null;

    $since = Carbon::now()->subDays(600);

    $mostReadPosts = Post::where('created_at', '>=', $since)
        ->orderByDesc('views') // la colonna nel DB è “view”
        ->limit(5)
        ->get();

    $mostCommentedPosts = Post::where('posts.created_at', '>=', $since)
        ->leftJoinSub(
            DB::table('fob_comments')
                ->selectRaw('reference_id, COUNT(*) as recent_comment_count')
                ->where('reference_type', Post::class)
                ->where('created_at', '>=', $since)
                ->groupBy('reference_id'),
            'recent_comments',
            'posts.id',
            '=',
            'recent_comments.reference_id',
        )
        ->orderByDesc('recent_comment_count')
        ->limit(5)
        ->get();

    $poll = null;
    $poll = PollOne::with('options')->where('active', true)->latest()->first();
    // Check if the poll exists and has options

    if ($poll) {
        $totalVotes = $poll->options->sum('votes');

        foreach ($poll->options as $option) {
            $option->percentage = $totalVotes > 0 ? round(($option->votes / $totalVotes) * 100) : 0;
        }
    }

    $ua = request()->header('User-Agent', '');

    // very small UA test – good enough for phone / tablet vs desktop
    $isMobile = preg_match('/android|iphone|ipod|ipad|blackberry|bb10|mini|windows\sce|palm/i', $ua);
@endphp


<div class="d-block d-md-none col-12 text-center">
    @include('ads.includes.MOBILE_HOME_HERO_25')
</div>

<section class="section recent-posts pt-20 pb-20"
    @if ($shortcode->background_color) style="background-color: {{ $shortcode->background_color }} !important;" @endif>
    <div class="container bg-white pb-4" style="max-width: 1200px">
        <div class="row">
            @php
                $topSidebarContent = $withSidebar ? dynamic_sidebar('top_sidebar') : null;

            @endphp

            <div @class([
                'col-lg-8' => $topSidebarContent,
                'col-12' => !$topSidebarContent,
            ])>
                <div class="page-content">
                    <div class="heading-container">
                        <h4 class="heading-partial-underline">ULTIME NOTIZIE</h4>
                    </div>
                    <div class="post-group post-group--single">
                        <div class="post-group__header">
                            <div class="row"></div>
                        </div>
                        <div class="post-group__content">
                            <div class="row recent-posts-container">
                                @php
                                    $minMainPostsLimit = intval(10);
                                    $mainPostsLimit = intval(50);
                                @endphp
                                <div class="col-md-12 col-sm-12 col-12">
                                    @foreach ($posts as $index => $post)
                                        <article class="post post__vertical post__vertical--single post-item"
                                            style="display: {{ $index < $minMainPostsLimit ? 'flex' : 'none' }}; align-items: center; margin-bottom: 5px;">
                                            <!-- Image on the left -->
                                            <div class="post__thumbnail" style=" width: 48%;">
                                                @php

                                                    $size = $isMobile ? 'thumb' : 'medium';
                                                @endphp

                                                {!! RvMedia::image($post->image, $post->name, $size, attributes: ['loading' => 'lazy']) !!}
                                                <a class="post__overlay" href="{{ $post->url }}"
                                                    title="{{ $post->name }}"></a>
                                            </div>

                                            <!-- Content (Title and Description) on the right -->
                                            <div class="post__content-wrap"
                                                style="flex: 2.5; padding-left: 20px;margin-top:5%">
                                                <header class="post__header">
                                                    @php

                                                        $date = $post->created_at;

                                                        if ($date->isToday()) {
                                                            // If the post was created today, show only hour and minute
                                                            $formattedDate = $date->format('H:i');
                                                        } elseif ($date->isYesterday()) {
                                                            // If it was yesterday, show "Ieri alle" followed by hour and minute
                                                            $formattedDate = 'Ieri alle ' . $date->format('H:i');
                                                        } else {
                                                            // Otherwise, show the day, abbreviated month (in Italian), and hour:minute
                                                            // Set locale to Italian for month names (ensure you have installed the appropriate locale)
                                                            $formattedDate = $date
                                                                ->locale('it')
                                                                ->translatedFormat('d M H:i');
                                                        }
                                                    @endphp
                                                    <div class="text-dark mb-1 post-desc">

                                                        @php
                                                            $categoryName = $post->categories->count()
                                                                ? strtoupper($post->categories->first()->name)
                                                                : 'NOTIZIE';
                                                        @endphp

                                                        <span class=" mb-1">
                                                            <span class="post__last4-badge">
                                                                {{ $categoryName }}</span> /
                                                        </span>

                                                        <span class="post__date">
                                                            {{ $formattedDate }}
                                                        </span>
                                                        @if ($post->in_aggiornamento)
                                                            <span class="post-group__left-red-badge ml-2"><span
                                                                    class='pulse-circle'></span> <span
                                                                    class="text-white">In
                                                                    Aggiornamento</span>
                                                            </span>
                                                        @endif

                                                    </div>
                                                    <h4 class="post__title" style="margin: 0;">
                                                        <a href="{{ $post->url }}" title="{{ $post->name }}"
                                                            style="text-decoration: none; color: inherit;">
                                                            {{ $post->name }}
                                                        </a>
                                                    </h4>
                                                </header>
                                                <div class="post__content">
                                                    <p style="margin: 10px 0 0;">{{ $post->description }}</p>
                                                    <span class=" text-dark mt-1 d-block"
                                                        style="font-family: 'Titillium Web', sans-serif; font-weight: 400; font-size: 0.9rem;color:#888">
                                                        @php
                                                            $post->comments_count = FriendsOfBotble\Comment\Models\Comment::where(
                                                                'reference_id',
                                                                $post->id,
                                                            )->count();
                                                        @endphp
                                                        Di <a
                                                            style="color: #8424e3;font-weight: 400;font-size: 0.9rem !important;"
                                                            href="/author/{{ $post->author->username }}">{{ $post->author->first_name }}
                                                            {{ $post->author->last_name }}</a> /
                                                        <a class="fw-bold" href="{{ $post->url }}#comments"
                                                            style="color:#8424e3;font-size:0.9rem !important;font-weight:400 !important;">
                                                            <i class="fa fa-comment" aria-hidden="true"></i>
                                                            {{ $post->comments_count > 0 ? $post->comments_count : 'Commenta' }}
                                                        </a>
                                                    </span>
                                                </div>

                                        </article>

                                        <!-- Optional ads -->
                                        @if ($index == 0)
                                            <div class="d-none d-md-block mb-2">
                                                @include('ads.includes.adsrecentp1')

                                            </div>
                                            <div class="d-block d-md-none col-12 mb-4 text-center">
                                                @include('ads.includes.MOBILE_POSIZIONE_1')
                                            </div>
                                        @endif
                                        @if ($index == 2)
                                            <div class="d-none d-md-block mb-2">
                                                @include('ads.includes.adsrecentp2')
                                            </div>

                                            <div class="d-block d-md-none col-12 mb-4 text-center">
                                                @include('ads.includes.MOBILE_POSIZIONE_2')
                                            </div>
                                        @endif
                                        @if ($index == 5)
                                            <div class="d-none d-md-block mb-2">
                                                @include('ads.includes.adsrecentp3')
                                            </div>

                                            <div class="d-block d-md-none col-12 mb-4 text-center">
                                                @include('ads.includes.MOBILE_POSIZIONE_3')
                                            </div>
                                        @endif
                                        @if ($index == 7)
                                            <div class="d-none d-md-block mb-2">
                                                @include('ads.includes.adsrecentp2')
                                            </div>
                                        @endif
                                        @if ($index == 10)
                                            <div class="tenth-place mb-2">
                                                @include('ads.includes.adsrecentp1')

                                            </div>
                                            <div class="d-block d-md-none col-12 mb-4 text-center">
                                                @include('ads.includes.MOBILE_POSIZIONE_5')
                                            </div>
                                        @endif
                                    @endforeach

                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>



            @if ($topSidebarContent)

                @php
                    $match = App\Models\Calendario::where('status', 'SCHEDULED')
                        ->orWhere('status', 'LIVE')
                        ->orderBy('match_date', 'asc')
                        ->first();
                    $home_team = json_decode($match->home_team, true);
                    $away_team = json_decode($match->away_team, true);

                @endphp
                <div class="col-lg-4">
                    <div class="page-sidebar">
                        @php
                            $widget = \App\Models\YtWidget::first();
                        @endphp
                        @if ($widget)
                            @php
                                $uniq = 'ytwidget-' . uniqid();
                            @endphp
                            <style>
                                /* === desktop / ≥ 576 px (unchanged) === */
                                #{{ $uniq }} {
                                    position: fixed;
                                    bottom: 20px;
                                    right: 20px;
                                    width: 340px;
                                    /*   16 : 9 — 340 × 192 px   */
                                    z-index: 9999;
                                    border-radius: 10px;
                                    box-shadow: 0 4px 14px rgba(0, 0, 0, .25);
                                    overflow: hidden;
                                }

                                #{{ $uniq }} .yt-header {
                                    display: flex;
                                    align-items: center;
                                    justify-content: space-between;
                                    padding: .4rem .75rem;
                                    background: #fff;
                                    color: #4b2d7f;
                                    font-weight: 600;
                                    border-bottom: 1px solid #e4e4e8;
                                }

                                #{{ $uniq }} .yt-header .yt-icon {
                                    width: 20px;
                                    height: 20px;
                                    fill: #4b2d7f;
                                    margin-right: .4rem;
                                }

                                #{{ $uniq }} .yt-header a {
                                    color: #4b2d7f;
                                    text-decoration: none;
                                    font-size: .875rem;
                                    padding: .2rem .6rem;
                                    border: 1px solid #4b2d7f;
                                    border-radius: 6px;
                                    transition: all .2s;
                                }

                                #{{ $uniq }} iframe {
                                    width: 100%;
                                    height: 200px;
                                    border: 0;
                                }

                                #{{ $uniq }} .yt-controls {
                                    display: flex;
                                    margin-top: -7px;
                                    justify-content: space-between;
                                    background: #4b2d7f;
                                }

                                #{{ $uniq }} .yt-controls button {
                                    flex: 1;
                                    color: #fff;
                                    border: 0;
                                    background: none;
                                    padding: .5rem;
                                }

                                #{{ $uniq }} .yt-controls button:hover {
                                    background: #37235c;
                                }

                                /* === phones / tablets (< 576 px) — stop floating === */
                                @media (max-width: 575.98px) {
                                    #{{ $uniq }} {
                                        position: static;
                                        /* back to normal flow  */
                                        width: 100%;
                                        /* full-width card      */
                                        margin: 0 0 1rem;
                                        /* bottom spacing       */
                                        bottom: auto;
                                        right: auto;
                                        box-shadow: none;
                                        /* optional: flatter    */
                                    }
                                }
                            </style>

                            <div id="{{ $uniq }}">
                                <div class="yt-header">
                                    <div class="d-flex align-items-center">
                                        <svg class="yt-icon" viewBox="0 0 24 24">
                                            <path
                                                d="M23.5 6.2a3 3 0 0 0-2.1-2.1C19 3.5 12 3.5 12 3.5s-7 0-9.4.6a3 3 0 0 0-2.1 2.1A31.7 31.7 0 0 0 0 12a31.7 31.7 0 0 0 .5 5.8 3 3 0 0 0 2.1 2.1c2.4.6 9.4.6 9.4.6s7 0 9.4-.6a3 3 0 0 0 2.1-2.1A31.7 31.7 0 0 0 24 12a31.7 31.7 0 0 0-.5-5.8zM9.6 15.5V8.5l6 3.5-6 3.5z" />
                                        </svg>
                                        YouTube
                                    </div>
                                    <a href="https://www.youtube.com/@laviola_it" target="_blank"
                                        rel="noopener">Seguici</a>
                                </div>
                                @if ($widget->type === 'live')

                                    <iframe
                                        src="https://www.youtube.com/embed/{{ \App\Models\YtWidget::extractId($widget->live_url) }}?rel=0"
                                        allowfullscreen></iframe>
                                @else
                                    {{-- ======= PLAYLIST with multiple iframes =============== --}}
                                    @php
                                        $ids = collect($widget->playlist_urls)
                                            ->map(fn($u) => \App\Models\YtWidget::extractId($u))
                                            ->values();
                                    @endphp

                                    <div id="{{ $uniq }}-deck">
                                        @foreach ($ids as $i => $vid)
                                            <iframe class="yt-frame {{ $i ? 'd-none' : '' }}"
                                                data-index="{{ $i }}" allowfullscreen {{-- load src only for the first item --}}
                                                @unless ($i) src="https://www.youtube.com/embed/{{ $vid }}?rel=0" @endunless></iframe>
                                        @endforeach
                                    </div>

                                    <div class="yt-controls">
                                        <button id="prev-{{ $uniq }}">&#9664;</button>
                                        <button id="next-{{ $uniq }}">&#9654;</button>
                                    </div>

                                    <script>
                                        (function() {
                                            const frames = [...document.querySelectorAll('#{{ $uniq }} .yt-frame')];
                                            if (!frames.length) return;

                                            let idx = 0;

                                            function show(i) {
                                                frames.forEach((f, k) => {
                                                    if (k === i) {
                                                        if (!f.src) { // lazy-load when first shown
                                                            const id = "{{ $ids->get(0) }}".replace(/.*/, () => {!! $ids !!}[k]);
                                                            f.src = "https://www.youtube.com/embed/" + id + "?rel=0";
                                                        }
                                                        f.classList.remove('d-none');
                                                    } else {
                                                        if (f.src) f.src = ''; // clear src to stop audio
                                                        f.classList.add('d-none');
                                                    }
                                                });
                                                idx = i;
                                            }

                                            document.getElementById('prev-{{ $uniq }}').onclick = () =>
                                                show((idx - 1 + frames.length) % frames.length);

                                            document.getElementById('next-{{ $uniq }}').onclick = () =>
                                                show((idx + 1) % frames.length);
                                        })();
                                    </script>
                                @endif
                            </div>
                        @endif


                        <section>
                            @if ($poll->position == 'top')
                                @include('polls.includes.poll-sidebar', $poll)
                            @endif
                            <div class="mb-4 row align-items-center upcoming-match upcoming-match-sidebar">
                                <!-- Match Date, Time, and Venue -->
                                <div class="col-md-12 text-center z-1">
                                    <p>{{ ucwords(\Carbon\Carbon::parse($match->match_date)->locale('it')->timezone('Europe/Rome')->isoFormat('dddd D MMMM [ore] H:mm'), " \t\r\n\f\v") }}
                                    </p>
                                </div>

                                <!-- Team Logos and Names -->
                                <div class="col-md-12 text-center z-1">
                                    <div class="row">
                                        <div class="col-6">
                                            <img src="{{ $home_team['logo'] }}" alt="{{ $home_team['name'] }} Crest"
                                                style="height: 30px; margin-bottom: 10px;">
                                            <h5>{{ $home_team['name'] }}</h5>
                                        </div>
                                        <div class="col-6">
                                            <img src="{{ $away_team['logo'] }}" alt="{{ $away_team['name'] }} Crest"
                                                style="height: 30px; margin-bottom: 10px;">
                                            <h5>{{ $away_team['name'] }}</h5>
                                        </div>
                                    </div>
                                </div>

                                <!-- Ticket Buttons -->
                                <div class="col-md-12 text-center mt-4 z-1">
                                    @if ($match->status == 'LIVE')
                                        <a href="/diretta?match_id={{ $match->match_id }}"
                                            class="btn-comment-submit text-white">VAI ALLA
                                            DIRETTA</a>
                                    @else
                                        <div id="countdown mt-10"
                                            style="background: #441274;padding:10px;border-radius:3px;">
                                            <i class="fa fa-clock-o" aria-hidden="true"></i> <span
                                                id="countdown-timer"></span>
                                        </div>
                                    @endif
                                </div>
                            </div>
                    </div>
                    @if (!$isMobile)
                        <div class="row mt-4 ad-top-sidebar">
                            @include('ads.includes.SIZE_300X250_TOP')
                        </div>
                    @endif
                    @include('last_post_editoriale')
                    <div class="widget widget__recent-post mt-4 mb-4">
                        <ul class="nav nav-tabs" id="postTabs" role="tablist">
                            <li class="nav-item" role="presentation">
                                <a class="nav-link active" id="recent-posts-tab" data-toggle="tab"
                                    href="#recent-posts" role="tab" aria-controls="recent-posts"
                                    aria-selected="true">
                                    I PIÙ LETTI
                                </a>
                            </li>
                            <li class="nav-item" role="presentation">
                                <a class="nav-link" id="most-commented-tab" data-toggle="tab" href="#most-commented"
                                    role="tab" aria-controls="most-commented" aria-selected="false">
                                    <span style="color: #8424e3; margin-right: 4px;"><i
                                            class="fas fa-bolt"></i></span>
                                    I PIÙ COMMENTATI
                                </a>
                            </li>
                        </ul>
                        <div class="tab-content" id="postTabsContent">
                            <div class="tab-pane fade show active" id="recent-posts" role="tabpanel"
                                aria-labelledby="recent-posts-tab">
                                <div class="widget__content">
                                    <ul>
                                        @foreach ($mostReadPosts as $post)
                                            <li>
                                                <article class="post post__widget d-flex align-items-start"
                                                    style="margin-bottom: 10px;">
                                                    {{-- Thumbnail on the left, fixed width --}}
                                                    <div class="post__thumbnail"
                                                        style="width: 80px; flex-shrink: 0; margin-right: 10px;">
                                                        {{ RvMedia::image($post->image, $post->name, 'thumb') }}
                                                        <a href="{{ $post->url }}" title="{{ $post->name }}"
                                                            class="post__overlay"></a>
                                                    </div>

                                                    {{-- Text content on the right --}}
                                                    <header class="post__header" style="flex: 1;">
                                                        {{-- Optional: Category label in uppercase, if you want it above the title --}}
                                                        @if ($post->categories->count())
                                                            <span class="category-span">
                                                                {{ strtoupper($post->categories->first()->name) }}
                                                            </span>
                                                        @endif

                                                        {{-- Post Title --}}
                                                        <h4 class="post__title" style="margin: 0;">
                                                            <a href="{{ $post->url }}"
                                                                title="{{ $post->name }}"
                                                                style="text-decoration: none; color: inherit;">
                                                                {{ $post->name }}
                                                            </a>
                                                        </h4>

                                                        {{-- Date --}}
                                                        <div class="post__meta date-span"
                                                            style="font-size: 0.75rem; color: #999; margin-top: 2px;">
                                                            <span class="post__created-at">
                                                                {{ Theme::formatDate($post->created_at) }}
                                                            </span>
                                                        </div>
                                                    </header>
                                                </article>
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                            <div class="tab-pane fade" id="most-commented" role="tabpanel"
                                aria-labelledby="most-commented-tab">
                                <div class="widget__content">
                                    <ul>
                                        @foreach ($mostCommentedPosts as $post)
                                            <li>
                                                <article class="post post__widget d-flex align-items-start"
                                                    style="margin-bottom: 10px;">
                                                    {{-- Thumbnail on the left, fixed width --}}
                                                    <div class="post__thumbnail"
                                                        style="width: 80px; flex-shrink: 0; margin-right: 10px;">
                                                        {{ RvMedia::image($post->image, $post->name, 'thumb') }}
                                                        <a href="{{ $post->url }}" title="{{ $post->name }}"
                                                            class="post__overlay"></a>
                                                    </div>

                                                    {{-- Text content on the right --}}
                                                    <header class="post__header" style="flex: 1;">
                                                        {{-- Optional: Category label in uppercase, if you want it above the title --}}
                                                        @if ($post->categories->count())
                                                            <span
                                                                style="display: block; font-size: 0.75rem; text-transform: uppercase; color: #999;">
                                                                {{ strtoupper($post->categories->first()->name) }}
                                                            </span>
                                                        @endif

                                                        {{-- Post Title --}}
                                                        <h4 class="post__title" style="margin: 0;">
                                                            <a href="{{ $post->url }}"
                                                                title="{{ $post->name }}"
                                                                style="text-decoration: none; color: inherit;">
                                                                {{ $post->name }}
                                                            </a>
                                                        </h4>

                                                        {{-- Date --}}
                                                        <div class="post__meta"
                                                            style="font-size: 0.75rem; color: #999; margin-top: 2px;">
                                                            <span class="post__created-at">
                                                                {{ Theme::formatDate($post->created_at) }}
                                                            </span>
                                                        </div>
                                                    </header>
                                                </article>
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                    @if (!$isMobile)
                        <div class="row mt-4 mb-4 ad-top-sidebar">
                            @include('ads.includes.SIZE_300X250_C1')
                        </div>
                    @endif


                    <div>
                        @php
                            $updateMessage = App\Http\Controllers\StandingController::fetchStandingsIfNeeded();
                            $updateScheduledMessage = App\Http\Controllers\StandingController::fetchScheduledMatches();
                        @endphp
                    </div>
                    <table class="table table-sm table-striped mt-4">
                        <thead
                            style="
                            background: blueviolet;
                            border: 1px solid white;
                            color: white;
                            font-weight: 900;
                        ">
                            <tr>
                                <th style="border-right: 1px solid white;font-weight:700">Classifica Serie A</th>
                                <th style="border-right: 1px solid white;">PT</th>
                                <th style="border-right: 1px solid white;">G</th>
                                <th style="border-right: 1px solid white;">V</th>
                                <th style="border-right: 1px solid white;">N</th>
                                <th style="border-right: 1px solid white;">P</th>
                                <th>DR</th>
                            </tr>
                        </thead>
                        <tbody
                            style="
                            background: white;
                            border: 1px solid white;
                        ">

                            @foreach (App\Models\Standing::all() as $index => $standing)
                                @php
                                    // Assign special styles or labels based on the position
                                    $rank = $index + 1;
                                    $labelClass = '';
                                    if ($rank <= 4) {
                                        $labelClass = 'badge badge-success'; // First place
                                    } elseif ($rank == 5) {
                                        $labelClass = 'badge badge-warning'; // Top 4
                                    } elseif ($rank == 6) {
                                        $labelClass = 'badge badge-warning'; // Top 6
                                    } elseif ($rank >= 18) {
                                        $labelClass = 'badge badge-danger'; // Top 6
                                    } else {
                                        $labelClass = 'text-dark badge badge-light'; // Top 6
                                    }
                                @endphp

                                <tr style="border-bottom:1px solid blueviolet">
                                    <td @if ($standing->short_name == 'Fiorentina') style="background-color:#441274 !important;color:white !important;" @endif
                                        style="border-right: 1px solid blueviolet;">
                                        <span
                                            class="{{ $labelClass }}"@if ($standing->short_name == 'Fiorentina') style='color:white !important' @endif>{{ $rank }}</span>
                                        <img src="{{ $standing->crest_url }}" width="15">
                                        {{ $standing->short_name }}
                                    </td>
                                    <td @if ($standing->short_name == 'Fiorentina') style="background-color:#441274 !important;color:white !important;" @endif
                                        style="border-right: 1px solid blueviolet;">
                                        {{ $standing->points }}
                                    </td>
                                    <td @if ($standing->short_name == 'Fiorentina') style="background-color:#441274 !important;color:white !important;text-align:center" @endif
                                        style="border-right: 1px solid blueviolet;text-align:center">
                                        {{ $standing->played_games }}
                                    </td>
                                    <td @if ($standing->short_name == 'Fiorentina') style="background-color:#441274 !important;color:white !important;text-align:center" @endif
                                        style="border-right: 1px solid blueviolet;text-align:center">
                                        {{ $standing->won }}
                                    </td>
                                    <td @if ($standing->short_name == 'Fiorentina') style="background-color:#441274 !important;color:white !important;text-align:center" @endif
                                        style="border-right: 1px solid blueviolet;text-align:center">
                                        {{ $standing->draw }}
                                    </td>
                                    <td @if ($standing->short_name == 'Fiorentina') style="background-color:#441274 !important;color:white !important;text-align:center" @endif
                                        style="border-right: 1px solid blueviolet;text-align:center">
                                        {{ $standing->lost }}
                                    </td>
                                    <td @if ($standing->short_name == 'Fiorentina') style="background-color:#441274 !important;color:white !important;text-align:center" @endif
                                        style="text-align:center">{{ $standing->goal_difference }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    <div class="legend mb-4">
                        <span class="badge badge-success"
                            style="display: inline-block; width: 15px; height: 15px; margin-right: 5px;"></span>
                        Champions League
                        <span class="badge badge-warning"
                            style="display: inline-block; width: 15px; height: 15px; margin-right: 5px;"></span>
                        Europa & Conference League
                        <br>
                        <span class="badge badge-danger"
                            style="display: inline-block; width: 15px; height: 15px; margin-right: 5px;"></span>
                        Serie B
                    </div>
                    @if (!$isMobile)
                        <div class="row mt-4 ad-top-sidebar">
                            @include('ads.includes.SIZE_300X250_B1')
                        </div>
                    @endif

                    @if ($poll->position == 'under_calendario')
                        @include('polls.includes.poll-sidebar', $poll)
                    @endif
                    @include('videos.includes.adsvideo', ['foo' => 'bar'])

                    @if ($poll->position == 'end')
                        @include('polls.includes.poll-sidebar', $poll)
                    @endif
</section>
</div>

<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.bundle.min.js"></script>



<style>
    .btn-purple {
        background-color: purple;
        color: white;
    }
</style>

</div>
@endif





</div>
</div>
</section>

{{-- @include('ads.includes.adsense', ['adClient' => 'ca-pub-6741446998584415', 'adSlot' => 'YYYYYYYYYY']) --}}
@include('ads.includes.adsense', ['adClient' => 'ca-pub-6741446998584415'])

<script>
    document.addEventListener('DOMContentLoaded', () => {

        /* ---------- settings ---------- */
        const BATCH = {{ $minMainPostsLimit }}; // 5
        const MAX = document.querySelectorAll('.post-item').length;
        let visible = {{ $minMainPostsLimit }}; // start with 5

        /* ---------- helper to toggle visibility ---------- */
        function showUpTo(limit) {
            document.querySelectorAll('.post-item').forEach((el, i) => {
                el.style.display = (i < limit) ? 'flex' : 'none';
            });
        }

        /* ---------- first render ---------- */
        showUpTo(visible);

        /* ---------- loading banner ---------- */
        const loading = document.createElement('div');
        loading.id = 'batch-loading';
        loading.textContent = 'Caricamento dei prossimi articoli…';
        Object.assign(loading.style, {
            display: 'none',
            textAlign: 'center',
            padding: '12px 0',
            fontWeight: '600',
            color: '#8424e3'
        });

        /* ---------- sentinel right after the list ---------- */
        const row = document.querySelector('.post-group__content .row');
        const sentinel = document.createElement('div');
        sentinel.id = 'auto-load-sentinel';
        row.append(loading, sentinel); // banner first, sentinel last

        /* ---------- intersection observer ---------- */
        const io = new IntersectionObserver(entries => {
            if (!entries[0].isIntersecting) return;
            if (visible >= MAX) return;

            // show "loading…" banner, then reveal batch after delay
            loading.style.display = 'block';
            setTimeout(() => {
                loadNextBatch();
            }, 800); // ≈0.8 s feel-good pause
        }, {
            rootMargin: '200px'
        });
        io.observe(sentinel);

        /* ---------- batch loader ---------- */
        function loadNextBatch() {
            visible = Math.min(visible + BATCH, MAX);
            showUpTo(visible);
            loading.style.display = 'none';

            if (visible >= MAX) { // done – stop observing
                io.disconnect();
            }
        }

        /* ---------- optional manual button still works ---------- */
        const btn = document.getElementById('load-more');
        if (btn) {
            btn.addEventListener('click', loadNextBatch);
            // hide the button once everything is visible
            const mo = new MutationObserver(() => {
                if (visible >= MAX) btn.style.display = 'none';
            });
            mo.observe(loading, {
                attributes: true,
                attributeFilter: ['style']
            });
        }

    });
</script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        @if ($match->status != 'LIVE')
            // Set the date we're counting down to
            var countDownDate = new Date(
                "{{ \Carbon\Carbon::parse($match->match_date)->timezone('Europe/Rome')->toIso8601String() }}"
            ).getTime();

            // Update the count down every 1 second
            var countdownFunction = setInterval(function() {
                // Get today's date and time
                var now = new Date().getTime();

                // Find the distance between now and the count down date
                var distance = countDownDate - now;

                // Time calculations for days, hours, minutes and seconds
                var days = Math.floor(distance / (1000 * 60 * 60 * 24));
                var hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                var minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
                var seconds = Math.floor((distance % (1000 * 60)) / 1000);

                // Build the countdown string
                var countdownString = "tra ";
                if (days > 0) {
                    countdownString += days + " giorn" + (days != 1 ? "i" : "o") + " ";
                }
                if (hours > 0) {
                    countdownString += hours + " or" + (hours != 1 ? "e" : "a") + " ";
                }
                if (minutes > 0) {
                    countdownString += minutes + " minut" + (minutes != 1 ? "i" : "o") + " ";
                }


                // Display the result in the element with id="countdown-timer"
                document.getElementById("countdown-timer").innerHTML = countdownString.trim();

                // If the count down is over, write some text
                if (distance < 0) {
                    clearInterval(countdownFunction);
                    document.getElementById("countdown-timer").innerHTML = "MATCH STARTED";
                }
            }, 1000);
        @endif
    });
</script>
