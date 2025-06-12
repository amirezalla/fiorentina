@php
    use Illuminate\Support\Facades\DB;
    use Botble\Blog\Models\Post;
    use Carbon\Carbon;
    use Illuminate\Support\Facades\Cache;
    use FriendsOfBotble\Comment\Models\Comment;
@endphp
@if ($posts->isNotEmpty())

    <section class="section hero-section pb-20"
        @if ($shortcode->background_color) style="background-color: #441274 !important;" @endif>

        @php
            $match = App\Models\Calendario::where('status', 'SCHEDULED')
                ->orWhere('status', 'LIVE')
                ->orderBy('match_date', 'asc')
                ->first();
            $home_team = json_decode($match->home_team, true);
            $away_team = json_decode($match->away_team, true);

        @endphp
        <div class="container mb-3">
            <div class="row justify-content-center" style="padding: 0">
                <div class="d-none d-md-block col-6 mx-auto" style="padding: 4px">
                    <div class="col-12">
                        @include('ads.includes.SIZE_468X60_TOP_SX')
                    </div>
                </div>
                <div class="d-none d-md-block col-6 mx-auto" style="padding: 0">
                    <div class="col-12">
                        @include('ads.includes.SIZE_468X60_TOP_DX')
                    </div>
                </div>
                <div class="d-block d-md-none col-12 text-center">
                    @include('ads.includes.MOBILE_HOME_TOP_24')
                </div>
            </div>
        </div>

        <div class="container mb-3 d-none d-md-block " style="padding: 0">
            <div class="row align-items-center upcoming-match">
                <!-- Match Date, Time, and Venue -->
                <div class="col-md-3">
                    <p>{{ ucwords(\Carbon\Carbon::parse($match->match_date)->locale('it')->timezone('Europe/Rome')->isoFormat('dddd D MMMM [ore] H:mm'), " \t\r\n\f\v") }}
                    </p>
                </div>

                <!-- Team Logos and Names -->
                <div class="col-md-6 text-center">
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
                <div class="col-md-3">
                    <div class="d-grid text-center">
                        @if ($match->status == 'LIVE')
                            <a href="https://laviola.collaudo.biz/diretta?match_id={{ $match->match_id }}"
                                class="btn-sm btn-primary mb-2 btn-comment-submit text-white"
                                style="grid-area: auto;">Vai
                                alla
                                diretta!</a>
                        @else
                            <div id="countdown1" style="background: #441274;padding:10px;border-radius:3px;">
                                <i class="fa fa-clock-o" aria-hidden="true"></i> <span id="countdown-timer1"></span>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        @php
            setlocale(LC_TIME, 'it_IT.UTF-8');
            Carbon::setLocale('it');

            /* ---------------- HERO (1-3) ---------------- */
            $heroPosts = Cache::remember('home.heroPosts', 18_000, function () {
                $orders = [1, 2, 3];

                $posts = Post::when(
                    Post::whereIn('hero_order', $orders)->exists(),
                    function ($q) use ($orders) {
                        $sub = Post::select('hero_order', DB::raw('MAX(updated_at) as max_updated'))
                            ->whereIn('hero_order', $orders)
                            ->groupBy('hero_order');

                        return $q
                            ->with('categories:id,name')
                            ->joinSub(
                                $sub,
                                'latest',
                                fn($j) => $j
                                    ->on('posts.hero_order', '=', 'latest.hero_order')
                                    ->on('posts.updated_at', '=', 'latest.max_updated'),
                            )
                            ->orderBy('posts.hero_order');
                    },
                    fn($q) => $q->with('categories:id,name')->latest('created_at')->take(3),
                )->get();

                /* niente cache se il risultato è vuoto */
                if ($posts->isEmpty()) {
                    return $posts; // sarà scartato più sotto
                }

                foreach ($posts as $p) {
                    $p->comments_count = Comment::where('reference_id', $p->id)->count();
                    $p->formatted_date = Carbon::parse($p->published_at)->translatedFormat('d M H:i');
                }

                return $posts;
            });

            /* se era vuoto, rigenera subito (senza salvarlo) */
            if ($heroPosts->isEmpty()) {
                Cache::forget('home.heroPosts');
                // rigenera “al volo” senza caching
                $heroPosts = (function () {
                    $orders = [1, 2, 3];

                    $posts = Post::when(
                        Post::whereIn('hero_order', $orders)->exists(),
                        function ($q) use ($orders) {
                            $sub = Post::select('hero_order', DB::raw('MAX(updated_at) as max_updated'))
                                ->whereIn('hero_order', $orders)
                                ->groupBy('hero_order');

                            return $q
                                ->with('categories:id,name')
                                ->joinSub(
                                    $sub,
                                    'latest',
                                    fn($j) => $j
                                        ->on('posts.hero_order', '=', 'latest.hero_order')
                                        ->on('posts.updated_at', '=', 'latest.max_updated'),
                                )
                                ->orderBy('posts.hero_order');
                        },
                        fn($q) => $q->with('categories:id,name')->latest('created_at')->take(3),
                    )->get();

                    /* niente cache se il risultato è vuoto */
                    if ($posts->isEmpty()) {
                        return $posts; // sarà scartato più sotto
                    }

                    foreach ($posts as $p) {
                        $p->comments_count = Comment::where('reference_id', $p->id)->count();
                        $p->formatted_date = Carbon::parse($p->published_at)->translatedFormat('d M H:i');
                    }

                    return $posts;
                })();
            }

            /* ---------------- BLACK-BOX (4-7) ---------------- */
            $lastRecentPosts = Cache::remember('home.lastRecentPosts', 18_000, function () {
                $orders = [4, 5, 6, 7];

                $posts = Post::when(
                    Post::whereIn('hero_order', $orders)->exists(),
                    function ($q) use ($orders) {
                        $sub = Post::select('hero_order', DB::raw('MAX(updated_at) as max_updated'))
                            ->whereIn('hero_order', $orders)
                            ->groupBy('hero_order');

                        return $q
                            ->with('categories:id,name')
                            ->joinSub(
                                $sub,
                                'latest',
                                fn($j) => $j
                                    ->on('posts.hero_order', '=', 'latest.hero_order')
                                    ->on('posts.updated_at', '=', 'latest.max_updated'),
                            )
                            ->orderBy('posts.hero_order');
                    },
                    fn($q) => $q->with('categories:id,name')->latest('created_at')->skip(3)->take(4),
                )->get();

                if ($posts->isEmpty()) {
                    return $posts; // non memorizzare “vuoto”
                }

                foreach ($posts as $p) {
                    $p->comments_count = Comment::where('reference_id', $p->id)->count();
                    $p->formatted_date = Carbon::parse($p->published_at)->translatedFormat('d M H:i');
                }

                return $posts;
            });

            if ($lastRecentPosts->isEmpty()) {
                Cache::forget('home.lastRecentPosts');
                $lastRecentPosts = (function () {
                    $orders = [4, 5, 6, 7];

                    $posts = Post::when(
                        Post::whereIn('hero_order', $orders)->exists(),
                        function ($q) use ($orders) {
                            $sub = Post::select('hero_order', DB::raw('MAX(updated_at) as max_updated'))
                                ->whereIn('hero_order', $orders)
                                ->groupBy('hero_order');

                            return $q
                                ->with('categories:id,name')
                                ->joinSub(
                                    $sub,
                                    'latest',
                                    fn($j) => $j
                                        ->on('posts.hero_order', '=', 'latest.hero_order')
                                        ->on('posts.updated_at', '=', 'latest.max_updated'),
                                )
                                ->orderBy('posts.hero_order');
                        },
                        fn($q) => $q->with('categories:id,name')->latest('created_at')->skip(3)->take(4),
                    )->get();

                    if ($posts->isEmpty()) {
                        return $posts; // non memorizzare “vuoto”
                    }

                    foreach ($posts as $p) {
                        $p->comments_count = Comment::where('reference_id', $p->id)->count();
                        $p->formatted_date = Carbon::parse($p->published_at)->translatedFormat('d M H:i');
                    }

                    return $posts;
                })();
            }
        @endphp


        <div class="container">
            <div class="row">
                {{-- ------------------------  COLONNA HERO  ------------------------ --}}
                <div class="col-12 col-lg-9 p-0 m-0">
                    <div class="post-group post-group--hero h-100">
                        @foreach ($heroPosts as $post)
                            @if ($loop->first)
                                {{-- CARD HERO GRANDE (LCP) --}}
                                <div class="post-group__left full-width">
                                    <article class="post post__inside post__inside--feature h-100">
                                        <div class="post__thumbnail h-100">
                                            {{ RvMedia::image(
                                                $post->image,
                                                $post->name,
                                                'featured',
                                                attributes: [
                                                    'loading' => 'eager',
                                                    'fetchpriority' => 'high',
                                                    'decoding' => 'async',
                                                    'width' => 565,
                                                    'height' => 375,
                                                ],
                                            ) }}
                                            <a class="post__overlay" href="{{ $post->url }}"
                                                title="{{ $post->name }}"></a>
                                        </div>

                                        {{-- header --}}
                                        <header class="post__header">
                                            <div class="d-flex">
                                                @if ($post->categories->count())
                                                    <span class="post-group__left-purple-badge mb-2">
                                                        {{ $post->categories->first()->name }}
                                                    </span>
                                                @endif

                                                @if ($post->in_aggiornamento)
                                                    <span class="post-group__left-red-badge mb-2 ml-2">
                                                        <span class="pulse-circle"></span> In Aggiornamento
                                                    </span>
                                                @endif
                                            </div>

                                            <h3 class="post__title">
                                                <a id="post-title-first"
                                                    href="{{ $post->url }}">{{ $post->name }}</a>
                                            </h3>

                                            <p class="post-desc-first d-none d-md-block" style="margin:3px 0 0;">
                                                {{ $post->description }}
                                            </p>

                                            <span class="text-dark mt-2 d-block post-desc">
                                                <span class="fw-bold author-post" style="color:#ffffff">
                                                    <a href="/author/{{ $post->author->username }}">{{ $post->author->first_name }}
                                                        {{ $post->author->last_name }}</a>
                                                </span> /
                                                <a class="fw-bold" href="{{ $post->url }}#comments"
                                                    style="color:#ffffff">
                                                    <i class="fa fa-comment"></i>
                                                    {{ $post->comments_count ?: 'Commenta' }}
                                                </a>
                                                <span class="created_at" style="color:#ffffff"> /
                                                    {{ $post->formatted_date }}
                                                </span>
                                            </span>
                                        </header>
                                    </article>
                                </div>

                                {{-- colonna destra della hero  --}}
                                <div class="post-group__right d-flex flex-column half-width">
                                @else
                                    {{-- CARD HERO PICCOLA --}}
                                    <div class="post-group__item w-100 flex-grow-1">
                                        <article
                                            class="post post__inside post__inside--feature post__inside--feature-small h-100">
                                            <div class="post__thumbnail h-100">
                                                {{ RvMedia::image(
                                                    $post->image,
                                                    $post->name,
                                                    'medium',
                                                    attributes: [
                                                        'loading' => 'lazy',
                                                        'decoding' => 'async',
                                                        'width' => 375,
                                                        'height' => 250,
                                                    ],
                                                ) }}
                                                <a class="post__overlay" href="{{ $post->url }}"
                                                    title="{{ $post->name }}"></a>
                                            </div>

                                            {{-- header --}}
                                            <header class="post__header">
                                                <div class="d-flex">
                                                    @if ($post->categories->count())
                                                        <span class="fz-14px post-group__left-purple-badge">
                                                            {{ $post->categories->first()->name }}
                                                        </span>
                                                    @endif
                                                    @if ($post->in_aggiornamento)
                                                        <span class="post-group__left-red-badge mb-2 ml-2">
                                                            <i class="fa fa-spinner text-white"></i> In Aggiornamento
                                                        </span>
                                                    @endif
                                                </div>

                                                <h3 class="post__title">
                                                    <a href="{{ $post->url }}">{{ $post->name }}</a>
                                                </h3>

                                                <span class="text-dark mt-2 d-block post-desc" style="">
                                                    <span class=" author-post" style="color:#ffffff">
                                                        <a href="/author/{{ $post->author->username }}">{{ $post->author->first_name }}
                                                            {{ $post->author->last_name }}</a>
                                                    </span> /
                                                    <a class="" href="{{ $post->url }}#comments"
                                                        style="color:#ffffff">
                                                        <i class="fa fa-comment"></i>
                                                        {{ $post->comments_count ?: 'Commenta' }}
                                                    </a>
                                                    <span class="created_at" style="color:#ffffff"> /
                                                        {{ $post->formatted_date }}
                                                    </span>
                                                </span>
                                            </header>
                                        </article>
                                    </div>

                                    @if ($loop->last)
                                </div>
                            @endif
                        @endif
@endforeach
</div>
</div>



{{-- -----------------------  COLONNA BLACK-BOX  ---------------------- --}}
<div class="col-12 col-lg-3 mx-0 px-0 d-none d-md-block">


    <div class="black-box px-3 py-3">
        <div class="d-flex flex-column justify-content-around h-100">
            @foreach ($lastRecentPosts as $post)
                <article class="w-100 @unless ($loop->last) mb-3 @endunless">
                    <header class="post__last4">
                        @if ($post->categories->count())
                            <div class="d-flex mb-1">
                                <span class="post__last4-badge">
                                    {{ $post->categories->first()->name }}
                                </span>
                            </div>
                        @endif

                        <a class="post__last4-text" href="{{ $post->url }}">{{ $post->name }}</a>

                        <span class="text-dark mt-2 d-block" style="font-size:x-small;">
                            <span class="fw-bold author-post" style="color:#ffffff">
                                <a style="color:#ffffff"
                                    href="/author/{{ $post->author->username }}">{{ $post->author->first_name }}
                                    {{ $post->author->last_name }}</a>
                            </span> /
                            <a class="fw-bold" href="{{ $post->url }}#comments" style="color:#ffffff">
                                <i class="fa fa-comment"></i>
                                {{ $post->comments_count ?: 'Commenta' }}
                            </a>
                            <span class="created_at" style="color:#ffffff"> /
                                {{ $post->formatted_date }}
                            </span>
                        </span>
                    </header>
                </article>
            @endforeach
        </div>
    </div>
</div>
</div>
</div>
</div>


</section>
@endif
