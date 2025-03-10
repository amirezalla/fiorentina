@php
    use Illuminate\Support\Facades\DB;
    use Botble\Blog\Models\Post;
@endphp
@if ($posts->isNotEmpty())

    <section class="section hero-section pt-20 pb-20"
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
            <div class="row justify-content-center">
                <div class="d-none d-md-block col-6 mx-auto">
                    <div class="col-12">
                        @include('ads.includes.SIZE_468X60_TOP_SX')
                    </div>
                </div>
                <div class="d-none d-md-block col-6 mx-auto">
                    <div class="col-12">
                        @include('ads.includes.SIZE_468X60_TOP_DX')
                    </div>
                </div>
                <div class="d-block d-md-none col-12 text-center">
                    @include('ads.includes.MOBILE_HOME_TOP_24')
                </div>
            </div>
        </div>

        <div class="container mb-3 ">
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
                        @if ($match->status == 'live')
                            <a href="https://laviola.collaudo.biz/diretta?match_id={{ $match->match_id }}"
                                class="btn-sm btn-primary mb-2 fiorentina-btn" style="grid-area: auto;">Vai alla
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

        <div class="container">
            <div class="row">
                <!-- Main content column (col-9) -->
                <div class="col-12 col-lg-9 p-0 m-0">
                    <div class="post-group post-group--hero h-100">
                        @php
                            $heroOrders = [1, 2, 3];

                            // Check if there are any posts with hero_order in [1, 2, 3]
                            $heroPostsCount = Post::whereIn('hero_order', $heroOrders)->count();

                            if ($heroPostsCount > 0) {
                                // Build a subquery that gets the latest updated_at for each hero_order value
                                $subquery = Post::select('hero_order', DB::raw('MAX(updated_at) as max_updated'))
                                    ->whereIn('hero_order', $heroOrders)
                                    ->groupBy('hero_order');

                                // Join the subquery to get only the most recently updated post for each hero_order value
                                $heroPosts = Post::with('categories:id,name')
                                    ->joinSub($subquery, 'latest', function ($join) {
                                        $join
                                            ->on('posts.hero_order', '=', 'latest.hero_order')
                                            ->on('posts.updated_at', '=', 'latest.max_updated');
                                    })
                                    ->orderBy('posts.hero_order')
                                    ->get();
                            } else {
                                // Fallback: if no posts have hero_order set for 1, 2, or 3,
                                // get the last 3 posts (ordered by created_at descending)
                                $heroPosts = Post::with('categories:id,name')
                                    ->orderBy('created_at', 'desc')
                                    ->take(3)
                                    ->get();
                            }
                        @endphp
                        @foreach ($heroPosts as $post)
                            @if ($loop->first)
                                <div class="post-group__left full-width">
                                    <article class="post post__inside post__inside--feature h-100">
                                        <div class="post__thumbnail h-100">
                                            {{ RvMedia::image($post->image, $post->name, 'featured', attributes: ['loading' => 'eager']) }}
                                            <a class="post__overlay" href="{{ $post->url }}"
                                                title="{{ $post->name }}"></a>
                                        </div>
                                        <header class="post__header">
                                            <div class="d-flex">
                                                @if ($post->categories->count())
                                                    <span
                                                        class="post-group__left-purple-badge mb-2">{{ $post->categories->first()->name }}</span>
                                                @endif
                                                @if ($post->in_aggiornamento)
                                                    <span class="post-group__left-red-badge mb-2 ml-2"><i
                                                            class="fa fa-spinner text-white"></i> In Aggiornamento
                                                    </span>
                                                @endif
                                            </div>

                                            <h3 class="post__title">
                                                <a href="{{ $post->url }}">{{ $post->name }}</a>
                                            </h3>
                                            <span class=" text-dark mt-3 d-block">
                                                @php
                                                    $post->comments_count = FriendsOfBotble\Comment\Models\Comment::where(
                                                        'reference_id',
                                                        $post->id,
                                                    )->count();
                                                @endphp
                                                <span class=" fw-bold author-post" style="color:#ffffff">
                                                    {{ $post->author->first_name }}
                                                    {{ $post->author->last_name }}</span> /
                                                <a class="fw-bold" href="{{ $post->url }}#comments"
                                                    style="color:#ffffff">
                                                    <i class="fa fa-comment" aria-hidden="true"></i>
                                                    {{ $post->comments_count > 0 ? $post->comments_count : 'Commenta' }}
                                                </a>
                                                <span class="created_at " style="color: gray;">
                                                    {{ $formattedDate }}
                                                </span>
                                            </span>
                                        </header>
                                    </article>
                                </div>
                                <div class="post-group__right d-flex flex-column half-width">
                                @else
                                    <div class="post-group__item w-100 flex-grow-1">
                                        <article
                                            class="post post__inside post__inside--feature post__inside--feature-small h-100">
                                            <div class="post__thumbnail h-100">
                                                {{ RvMedia::image($post->image, $post->name, 'medium', attributes: ['loading' => 'eager']) }}
                                                <a class="post__overlay" href="{{ $post->url }}"
                                                    title="{{ $post->name }}"></a>
                                            </div>
                                            <header class="post__header">
                                                <div class="d-flex">
                                                    @if ($post->categories->count())
                                                        <span
                                                            class="fz-14px post-group__left-purple-badge">{{ $post->categories->first()->name }}</span>
                                                    @endif
                                                    @if ($post->in_aggiornamento)
                                                        <span class="post-group__left-red-badge mb-2 ml-2"><i
                                                                class="fa fa-spinner text-white"></i> In Aggiornamento
                                                        </span>
                                                    @endif
                                                </div>
                                                <h3 class="post__title">
                                                    <a href="{{ $post->url }}">{{ $post->name }}</a>
                                                </h3>
                                                <span class=" text-dark mt-3 d-block">
                                                    @php
                                                        $post->comments_count = FriendsOfBotble\Comment\Models\Comment::where(
                                                            'reference_id',
                                                            $post->id,
                                                        )->count();
                                                    @endphp
                                                    <span class=" fw-bold author-post" style="color:#ffffff">
                                                        {{ $post->author->first_name }}
                                                        {{ $post->author->last_name }}</span> /
                                                    <a class="fw-bold" href="{{ $post->url }}#comments"
                                                        style="color:#ffffff">
                                                        <i class="fa fa-comment" aria-hidden="true"></i>
                                                        {{ $post->comments_count > 0 ? $post->comments_count : 'Commenta' }}
                                                    </a>
                                                    <span class="created_at " style="color: gray;">
                                                        {!! BaseHelper::renderIcon('ti ti-clock') !!} {{ $formattedDate }}
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

<!-- Black box column (col-3) similar to the image -->
<div class="col-12 col-lg-3 mx-0 px-0">
    @php

        $heroOrders = [4, 5, 6, 7];

        // Check if there are any posts with hero_order set to one of the given values.
        $heroPostsCount = Post::whereIn('hero_order', $heroOrders)->count();

        if ($heroPostsCount > 0) {
            // Get, for each hero_order value, the latest post (by updated_at)
            $subquery = Post::select('hero_order', DB::raw('MAX(updated_at) as max_updated'))
                ->whereIn('hero_order', $heroOrders)
                ->groupBy('hero_order');

            $lastRecentPosts = Post::with('categories:id,name')
                ->joinSub($subquery, 'latest', function ($join) {
                    $join
                        ->on('posts.hero_order', '=', 'latest.hero_order')
                        ->on('posts.updated_at', '=', 'latest.max_updated');
                })
                ->orderBy('posts.hero_order')
                ->get();
        } else {
            // Fallback: If no posts have hero_order set, skip the most recent 3 posts
            // and get the next 4 posts ordered by created_at descending.
            $lastRecentPosts = Post::with('categories:id,name')->orderBy('created_at', 'desc')->skip(3)->take(4)->get();
        }
    @endphp
    <div class="black-box px-3 py-3">
        <div class="d-flex flex-column justify-content-around h-100">
            @foreach ($lastRecentPosts as $post)
                <article class="w-100 @unless ($loop->last) mb-3 @endunless">
                    <header class="post__last4">
                        @if ($post->categories->count())
                            <div class="d-flex mb-1">
                                <span class="post__last4-badge">
                                    {{ $post->categories->first()->name }}</span>
                            </div>
                        @endif
                        <a class="post__last4-text" href="{{ $post->url }}">{{ $post->name }}</a>
                        <span class=" text-dark mt-3 d-block">
                            @php
                                $post->comments_count = FriendsOfBotble\Comment\Models\Comment::where(
                                    'reference_id',
                                    $post->id,
                                )->count();
                            @endphp
                            <span class=" fw-bold author-post" style="color:#ffffff">
                                {{ $post->author->first_name }}
                                {{ $post->author->last_name }}</span> /
                            <a class="fw-bold" href="{{ $post->url }}#comments" style="color:#ffffff">
                                <i class="fa fa-comment" aria-hidden="true"></i>
                                {{ $post->comments_count > 0 ? $post->comments_count : 'Commenta' }}
                            </a>
                            <span class="created_at " style="color: gray;">
                                {!! BaseHelper::renderIcon('ti ti-clock') !!} {{ $formattedDate }}
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
