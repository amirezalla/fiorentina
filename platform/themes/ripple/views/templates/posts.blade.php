@if ($posts->isNotEmpty())
    @foreach ($posts as $post)
        <article class="post post__horizontal mb-40 clearfix">
            <div class="post__thumbnail">
                {{ RvMedia::image($post->image, $post->name, 'medium') }}
                <a class="post__overlay" href="{{ $post->url }}" title="{{ $post->name }}"></a>
            </div>
            <div class="post__content-wrap">
                <header class="post__header">
                    <h3 class="post__title">
                        <a href="{{ $post->url }}" title="{{ $post->name }}">{{ $post->name }}</a>
                    </h3>
                </header>
                <div class="post__content p-0">
                    <p data-number-line="4">{!! $post->description !!}</p>
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
