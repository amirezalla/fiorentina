@if ($posts->isNotEmpty())
    @php
        $minMainPostsLimit = intval(10);
        $mainPostsLimit = intval(50);
        $ua = request()->header('User-Agent', '');

        // very small UA test – good enough for phone / tablet vs desktop
        $isMobile = preg_match('/android|iphone|ipod|ipad|blackberry|bb10|mini|windows\sce|palm/i', $ua);
    @endphp
    @foreach ($posts->loadMissing('author') as $index => $post)
        <article class="post post__vertical post__vertical--single post-item"
            style="display: {{ $index < $minMainPostsLimit ? 'flex' : 'none' }}; align-items: center; margin-bottom: 5px;">

            <!-- Image on the left -->
            <div class="post__thumbnail" style="width: 48%;">
                @php
                    $size = $isMobile ? 'thumb' : 'medium';
                @endphp

                {!! RvMedia::image($post->image, $post->name, $size, attributes: ['loading' => 'lazy']) !!}
                <a class="post__overlay" href="{{ $post->url }}" title="{{ $post->name }}"></a>
            </div>

            <!-- Content (Title and Description) on the right -->
            <div class="post__content-wrap" style="flex: 2.5; padding-left: 20px; margin-top: 5%">
                <header class="post__header" style="border:none">
                    @php
                        $date = $post->created_at;

                        if ($date->isToday()) {
                            $formattedDate = $date->format('H:i');
                        } elseif ($date->isYesterday()) {
                            $formattedDate = 'Ieri alle ' . $date->format('H:i');
                        } else {
                            $formattedDate = $date->locale('it')->translatedFormat('d M H:i');
                        }

                        $categoryName = $post->categories->count()
                            ? strtoupper($post->categories->first()->name)
                            : 'NOTIZIE';
                    @endphp

                    <div class="text-dark mb-1 post-desc">
                        <span class="mb-1" style="color:#aaa;font-size:0.8rem;">
                            <span class="post__last4-badge">{{ $categoryName }}</span> /
                        </span>

                        <span class="post__date">
                            {{ $formattedDate }}
                        </span>

                        @if ($post->in_aggiornamento)
                            <span class="post-group__left-red-badge ml-2">
                                <span class="pulse-circle"></span>
                                <span class="text-white">In Aggiornamento</span>
                            </span>
                        @endif
                    </div>

                    <h4 class="post__title" style="margin: 0;">
                        <a href="{{ $post->url }}" title="{{ $post->name }}"
                            style="text-decoration: none; color: #000;font-size: 1.6rem;
    font-weight: 700;
    letter-spacing: -0.02rem;
    line-height: 1.1;">
                            {{ $post->name }}
                        </a>
                    </h4>
                </header>

                <div class="post__content" style="padding: 0">
                    <p style="margin: 10px 0 0;">{{ $post->description }}</p>

                    <span class="text-dark mt-1 d-block"
                        style="font-family: 'Titillium Web', sans-serif; font-weight: 400; font-size: 0.9rem; color:#888;">
                        @php
                            $post->comments_count = FriendsOfBotble\Comment\Models\Comment::where(
                                'reference_id',
                                $post->id,
                            )->count();
                        @endphp
                        Di
                        <a style="color:#8424e3; font-weight:400; font-size:0.9rem !important;"
                            href="/author/{{ $post->author->username }}">
                            {{ $post->author->first_name }} {{ $post->author->last_name }}
                        </a>
                        /
                        <a class="fw-bold" href="{{ $post->url }}#comments"
                            style="color:#8424e3; font-size:0.9rem !important; font-weight:400 !important;">
                            <i class="fa fa-comment" aria-hidden="true"></i>
                            {{ $post->comments_count > 0 ? $post->comments_count : 'Commenta' }}
                        </a>
                    </span>
                </div>
            </div>
        </article>

        {{-- Ads between posts --}}
        @php($i = $loop->index)

        {{-- After post #0 --}}
        @if ($i === 0)
            <div class="d-none d-md-block my-3">
                @include('ads.includes.adsrecentp1')
            </div>
            <div class="d-block d-md-none col-12 my-3 text-center">
                @include('ads.includes.MOBILE_POSIZIONE_1')
            </div>
        @endif

        {{-- After post #2 --}}
        @if ($i === 2)
            <div class="d-none d-md-block my-3">
                @include('ads.includes.adsrecentp2')
            </div>
            <div class="d-block d-md-none col-12 my-3 text-center">
                @include('ads.includes.MOBILE_POSIZIONE_2')
            </div>
        @endif

        {{-- After post #5 --}}
        @if ($i === 5)
            <div class="d-none d-md-block my-3">
                @include('ads.includes.adsrecentp3')
            </div>
            <div class="d-block d-md-none col-12 my-3 text-center">
                @include('ads.includes.MOBILE_POSIZIONE_3')
            </div>
        @endif

        {{-- After post #7 (desktop only) --}}
        @if ($i === 7)
            <div class="d-none d-md-block my-3">
                @include('ads.includes.adsrecentp2')
            </div>
        @endif

        {{-- After post #10 --}}
        @if ($i === 10)
            <div class="my-3">
                @include('ads.includes.adsrecentp1')
            </div>
            <div class="d-block d-md-none col-12 my-3 text-center">
                @include('ads.includes.MOBILE_POSIZIONE_5')
            </div>
        @endif
    @endforeach

    <div class="page-pagination text-right">
        {!! $posts->withQueryString()->links() !!}
    </div>
@endif

<style>
    .section.pt-50.pb-100 {
        background-color: #ecf0f1;
    }
</style>
