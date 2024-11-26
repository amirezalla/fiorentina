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
                    <p>{{ ucwords(\Carbon\Carbon::parse($match->match_date)->locale('it')->timezone('Europe/Rome')->isoFormat('dddd D MMMM [ore] H:mm')," \t\r\n\f\v") }}
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
                    <div class="d-grid">
                        <a @if ($match->status == 'live') href="https://laviola.collaudo.biz/diretta?match_id={{ $match->match_id }}" @else disabled @endif
                            class="btn-sm btn-primary mb-2 fiorentina-btn" style="grid-area: auto;">Vai alla
                            diretta!</a>
                    </div>
                </div>
            </div>
        </div>

        <div class="container">
            <div class="row">
                <!-- Main content column (col-9) -->
                <div class="col-12 col-lg-9 p-0 m-0">
                    <div class="post-group post-group--hero h-100">
                        @foreach ($posts as $post)
                            @if ($loop->first)
                                <div class="post-group__left full-width">
                                    <article class="post post__inside post__inside--feature h-100">
                                        <div class="post__thumbnail h-100">
                                            {{ RvMedia::image($post->image, $post->name, 'featured', attributes: ['loading' => 'eager']) }}
                                            <a class="post__overlay" href="{{ $post->url }}"
                                                title="{{ $post->name }}"></a>
                                        </div>
                                        <header class="post__header">
                                            @if ($post->categories->count())
                                                <div class="d-flex">
                                                    <span
                                                        class="post-group__left-purple-badge mb-2">{{ $post->categories->first()->name }}</span>
                                                </div>
                                            @endif
                                            <h3 class="post__title">
                                                <a href="{{ $post->url }}">{{ $post->name }}</a>
                                            </h3>
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
                                                @if ($post->categories->count())
                                                    <div class="d-flex">
                                                        <span
                                                            class="fz-14px post-group__left-purple-badge">{{ $post->categories->first()->name }}</span>
                                                    </div>
                                                @endif
                                                <h3 class="post__title">
                                                    <a href="{{ $post->url }}">{{ $post->name }}</a>
                                                </h3>
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
        $lastRecentPosts = Botble\Blog\Models\Post::with('categories:id,name')
            ->orderBy('created_at', 'desc')
            ->take(4)
            ->get();
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
                    </header>
                </article>
            @endforeach
        </div>
    </div>
    {{--                        <h6 style="color: grey; font-size: 10px; margin-bottom: 5px;">NOTIZIE</h6> --}}
    {{--                        <p style="font-size: 14px;color: white;  margin-bottom: 20px;">Fiorentina, il programma di oggi</p> --}}

    {{--                        <h6 style="color: grey; font-size: 10px; margin-bottom: 5px;">NOTIZIE</h6> --}}
    {{--                        <p style="font-size: 14px;color: white;  margin-bottom: 20px;">Mutu: "Gud? Ho solo pensieri positivi, ma quando hai la 10 devi sempre dimostrare qualcosa in più"</p> --}}

    {{--                        <h6 style="color: grey; font-size: 10px; margin-bottom: 5px;">NOTIZIE</h6> --}}
    {{--                        <p style="font-size: 14px;color: white; margin-bottom: 20px;">Palladino (sala stampa): "Kean come un leone, De Gea è stato un fenomeno"</p> --}}

    {{--                        <h6 style="color: grey; font-size: 10px; margin-bottom: 5px;">NOTIZIE</h6> --}}
    {{--                        <p style="font-size: 14px;color: white; ">Commisso: "È stato un weekend perfetto. Domani parto, torno a gennaio"</p> --}}
</div>
</div>
</div>
</div>


</section>
@endif
