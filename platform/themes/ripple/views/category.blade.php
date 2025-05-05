@php Theme::set('section-name', $category->name) @endphp

@if ($posts->isNotEmpty())
    @foreach ($posts->loadMissing('author') as $post)
        {{-- FEATURED BLOCK – only for the first post --}}
        @if ($loop->first)
            <article class="post post__inside post__inside--feature h-100 text-center"> {{-- <- text-center keeps it tidy --}}
                <div class="post__thumbnail h-100">
                    {{ RvMedia::image($post->image, $post->name, 'featured', attributes: ['loading' => 'eager']) }}
                    <a class="post__overlay" href="{{ $post->url }}" title="{{ $post->name }}"></a>
                </div>

                <header class="post__header">
                    <div class="d-flex justify-content-center"> {{-- also centered --}}
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
                        <a id="post-title-first" href="{{ $post->url }}">{{ $post->name }}</a>
                    </h3>

                    <p class="post-desc-first d-none d-md-block my-2">
                        {{ $post->description }}
                    </p>

                    {{-- meta row --}}
                    <span class="text-dark d-block">
                        @php
                            $date = \Carbon\Carbon::parse($post->published_at)->locale('it');
                            $formattedDate = $date->translatedFormat('d M H:i');
                            $post->comments_count = FriendsOfBotble\Comment\Models\Comment::where(
                                'reference_id',
                                $post->id,
                            )->count();
                        @endphp

                        <span class="fw-bold author-post text-white">
                            {{ $post->author->first_name }} {{ $post->author->last_name }}
                        </span>
                        /
                        <a class="fw-bold" href="{{ $post->url }}#comments" style="color:#ffffff">
                            <i class="fa fa-comment" aria-hidden="true"></i>
                            {{ $post->comments_count ?: 'Commenta' }}
                        </a>
                        /
                        <span class="created_at text-white">{{ $formattedDate }}</span>
                    </span>
                </header>
            </article>
            @continue {{-- skip to next loop iteration so the feature post doesn’t get printed twice --}}
        @endif

        {{-- STANDARD HORIZONTAL CARD (unchanged) --}}
        <article class="post post__horizontal mb-40 clearfix">
            <div class="post__thumbnail">
                {{ RvMedia::image($post->image, $post->name, 'thumbnail') }}
                <a href="{{ $post->url }}" title="{{ $post->name }}" class="post__overlay"></a>
            </div>
            <div class="post__content-wrap">
                <header class="post__header">
                    <h3 class="post__title">
                        <a href="{{ $post->url }}" title="{{ $post->name }}">{{ $post->name }}</a>
                    </h3>
                    <div class="post__content">
                        <p data-number-line="4">{{ $post->description }}</p>
                        <span class="text-dark mt-3 d-block">
                            @php
                                $post->comments_count = FriendsOfBotble\Comment\Models\Comment::where(
                                    'reference_id',
                                    $post->id,
                                )->count();
                            @endphp
                            Di <span class="fw-bold author-post" style="color:#8424e3">
                                {{ $post->author->first_name }} {{ $post->author->last_name }}
                            </span> /
                            <a class="fw-bold" href="{{ $post->url }}#comments" style="color:#8424e3">
                                <i class="fa fa-comment" aria-hidden="true"></i>
                                {{ $post->comments_count ?: 'Commenta' }}
                            </a>
                        </span>
                    </div>
                </header>
            </div>
        </article>
    @endforeach

    <div class="page-pagination text-right">
        {!! $posts->links() !!}
    </div>
@else
    <div class="alert alert-warning">
        <p class="mb-0">{{ __('There is no data to display!') }}</p>
    </div>
@endif
