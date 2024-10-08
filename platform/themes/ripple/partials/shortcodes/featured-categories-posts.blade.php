<section class="section pt-50 pb-50">
    <div class="container bg-white">
        <div class="row" style="margin-top: -68px">
            <div class="col-lg-12">
                <div class="page-content">
                    <div class="post-group post-group--single">
                        <div class="post-group__header">
                            <h3 class="post-group__title">{{ $title }}</h3>
                        </div>
                        <div class="post-group__content">
                            <div class="row">
                                @foreach ($posts->chunk(3) as $chunk)
                                    <div class="col-12">
                                        @foreach ($chunk as $post)
                                            @if ($loop->first)
                                                <article
                                                    class="post post__vertical post__vertical--single post__vertical--simple">
                                                    <div class="post__thumbnail">
                                                        {{ RvMedia::image($post->image, $post->name, 'medium') }}
                                                        <a class="post__overlay" href="{{ $post->url }}"
                                                            title="{{ $post->name }}"></a>
                                                    </div>
                                                    <div class="post__content-wrap">
                                                        <header class="post__header">
                                                            <h3 class="post__title"><a href="{{ $post->url }}"
                                                                    title="{{ $post->name }}">{{ $post->name }}</a>
                                                            </h3>
                                                            <div class="post__meta">
                                                                <span
                                                                    class="created__month">{{ $post->created_at->translatedFormat('M') }}</span>
                                                                <span
                                                                    class="created__date">{{ $post->created_at->translatedFormat('d') }}</span>
                                                                <span
                                                                    class="created__year">{{ $post->created_at->translatedFormat('Y') }}</span>
                                                            </div>
                                                        </header>
                                                        <div class="post__content">
                                                            <p data-number-line="2">{{ $post->description }}</p>
                                                        </div>
                                                    </div>
                                                </article>
                                            @else
                                                <article
                                                    class="post post__horizontal post__horizontal--single mb-20 clearfix">
                                                    <div class="post__thumbnail">
                                                        {{ RvMedia::image($post->image, $post->name, 'medium') }}
                                                        <a class="post__overlay" href="{{ $post->url }}"
                                                            title="{{ $post->name }}"></a>
                                                    </div>
                                                    <div class="post__content-wrap">
                                                        <header class="post__header">
                                                            <h3 class="post__title"><a href="{{ $post->url }}"
                                                                    title="{{ $post->name }}">{{ $post->name }}</a>
                                                            </h3>
                                                            <div class="post__meta">
                                                                <span
                                                                    class="post__created-at">{{ Theme::formatDate($post->created_at) }}</span>
                                                            </div>
                                                        </header>
                                                    </div>
                                                </article>
                                            @endif
                                        @endforeach
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</section>
