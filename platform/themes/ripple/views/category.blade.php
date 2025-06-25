@php Theme::set('section-name', $category->name) @endphp

@if ($posts->isNotEmpty())
    @foreach ($posts->loadMissing('author') as $post)
        {{-- FEATURED BLOCK – only for the first post --}}
        @if ($loop->first)
            <article class="mb-4 post post__inside post__inside--feature h-100 text-center category-article">
                {{-- <- text-center keeps it tidy --}}
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

                    <h2 class="post__title">
                        <a id="post-title-first" href="{{ $post->url }}">{{ $post->name }}</a>
                    </h2>

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
                            <a href="/author/{{ $post->author->username }}">{{ $post->author->first_name }}
                                {{ $post->author->last_name }}</a>
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
        <article class="post post__vertical post__vertical--single post-item"
            style="display: {{ $index < $minMainPostsLimit ? 'flex' : 'none' }}; align-items: center; margin-bottom: 5px;">
            <!-- Image on the left -->
            <div class="post__thumbnail" style=" width: 48%;">
                @php

                    $size = $isMobile ? 'thumb' : 'medium';
                @endphp

                {!! RvMedia::image($post->image, $post->name, $size, attributes: ['loading' => 'lazy']) !!}
                <a class="post__overlay" href="{{ $post->url }}" title="{{ $post->name }}"></a>
            </div>

            <!-- Content (Title and Description) on the right -->
            <div class="post__content-wrap" style="flex: 2.5; padding-left: 20px;">
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
                            $formattedDate = $date->locale('it')->translatedFormat('d M H:i');
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
                            <span class="post-group__left-red-badge ml-2"><span class='pulse-circle'></span> <span
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
                        style="font-family: 'Titillium Web', sans-serif; font-weight: 600; font-size: 16px;color:#888">
                        @php
                            $post->comments_count = FriendsOfBotble\Comment\Models\Comment::where(
                                'reference_id',
                                $post->id,
                            )->count();
                        @endphp
                        Di <a style="color: #8424e3;font-weight: 700;"
                            href="/author/{{ $post->author->username }}">{{ $post->author->first_name }}
                            {{ $post->author->last_name }}</a> /
                        <a class="fw-bold" href="{{ $post->url }}#comments"
                            style="color:#8424e3;font-size:0.9rem !important;">
                            <i class="fa fa-comment" aria-hidden="true"></i>
                            {{ $post->comments_count > 0 ? $post->comments_count : 'Commenta' }}
                        </a>
                    </span>
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
