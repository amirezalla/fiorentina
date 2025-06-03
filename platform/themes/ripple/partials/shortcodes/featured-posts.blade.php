@php
    use Illuminate\Support\Facades\DB;
    use Botble\Blog\Models\Post;
    use Carbon\Carbon;
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

        <div class="container mb-3 " style="padding: 0">
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

        {{-- ======================  HERO  ====================== --}}
        @php
            // 1. carica i post da mostrare
            $heroOrders = [1, 2, 3];
            $heroPosts = Post::with('categories:id,name')
                ->when(
                    Post::whereIn('hero_order', $heroOrders)->exists(),
                    function ($q) use ($heroOrders) {
                        $sub = Post::select('hero_order', DB::raw('MAX(updated_at) as max_updated'))
                            ->whereIn('hero_order', $heroOrders)
                            ->groupBy('hero_order');
                        $q->joinSub(
                            $sub,
                            'latest',
                            fn($j) => $j
                                ->on('posts.hero_order', '=', 'latest.hero_order')
                                ->on('posts.updated_at', '=', 'latest.max_updated'),
                        )->orderBy('posts.hero_order');
                    },
                    fn($q) => $q->orderByDesc('created_at')->take(3),
                )
                ->get();
        @endphp

        {{-- 2. hint critici nel <head> –  stampati UNA sola volta --}}
        @once
            @push('head')
                <link rel="dns-prefetch" href="//laviola.s3.eu-south-1.wasabisys.com">
                <link rel="preconnect" href="https://laviola.s3.eu-south-1.wasabisys.com" crossorigin>
                <link rel="preload" as="image"
                    href="{{ RvMedia::getImageUrl(optional($heroPosts->first())->image, 'featured') }}"
                    imagesrcset="{{ RvMedia::getImageUrl(optional($heroPosts->first())->image, 'featured') }} 565w,
                           {{ RvMedia::getImageUrl(optional($heroPosts->first())->image, 'medium') }} 375w"
                    imagesizes="(min-width:768px) 565px,100vw" fetchpriority="high" crossorigin="anonymous">
            @endpush
        @endonce

        <div class="container">
            <div class="row">
                {{-- COLONNA PRINCIPALE -------------------------------------------------- --}}
                <div class="col-12 col-lg-9 p-0 m-0">
                    <div class="post-group post-group--hero h-100">
                        @foreach ($heroPosts as $post)
                            @if ($loop->first)
                                {{--  >>> LCP  ---------------------------------------------------- --}}
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
                                                    'crossorigin' => 'anonymous',
                                                    'width' => 565,
                                                    'height' => 375,
                                                ],
                                            ) }}
                                            <a class="post__overlay" href="{{ $post->url }}"
                                                title="{{ $post->name }}"></a>
                                        </div>
                                        @include('partials.post-meta-large', ['post' => $post])
                                    </article>
                                </div>
                                <div class="post-group__right d-flex flex-column half-width">
                                @else
                                    {{--  >>> card secondarie  ---------------------------------------- --}}
                                    <div class="post-group__item w-100 flex-grow-1">
                                        <article class="post post__inside post__inside--feature-small h-100">
                                            <div class="post__thumbnail h-100">
                                                {{ RvMedia::image(
                                                    $post->image,
                                                    $post->name,
                                                    'medium',
                                                    attributes: [
                                                        'loading' => 'lazy', // ⚠️ ora è lazy
                                                        'decoding' => 'async',
                                                        'width' => 375,
                                                        'height' => 250,
                                                    ],
                                                ) }}
                                                <a class="post__overlay" href="{{ $post->url }}"
                                                    title="{{ $post->name }}"></a>
                                            </div>
                                            @include('partials.post-meta-small', ['post' => $post])
                                        </article>
                                    </div>
                                    @if ($loop->last)
                                </div>
                            @endif
                        @endif
@endforeach
</div>
</div>

{{-- COLONNA NERA -------------------------------------------------------- --}}
<div class="col-12 col-lg-3 mx-0 px-0">
    @include('partials.black-box') {{-- la tua logica dei post 4-7 resta invariata --}}
</div>
</div>
</div>

</div>


</section>
@endif
