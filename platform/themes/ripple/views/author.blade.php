    {{-- everything here replaces {!! Theme::content() !!} --}}
    {{-- Header section mimicking the screenshot --}}
    @php
        use Botble\Media\Models\MediaFile;

    @endphp
    @php Theme::set('section-name', $user->first_name) @endphp

    <div class="text-center py-60">
        {{-- circular logo – swap the file if you have a different one --}}
        @php
            $img = RvMedia::image($user->avatar->url, $user->first_name, 'thumbnail');
        @endphp
        <div class="container" id="mvp-author-top-left">
            <div class="row pt-5">
                <div class="col-3">
                    {{ $img }} </div>
                <div class="col-9">
                    <h1 class="mvp-author-top-head left display-5 font-weight-bold">{{ $user->first_name }}
                        {{ $user->last_name }}</h1>
                </div>
            </div>

        </div>




    </div>

    <h4 class="section-title mb-40 mvp-sec-head mt-5">
        <span class="mvp-sec-head font-weight-700">
            {{ __('Notizie di') }} {{ $user->first_name }} {{ $user->last_name }}
        </span>
    </h4>

    {{-- Post loop --}}
    @php
        $minMainPostsLimit = intval(5);
        $mainPostsLimit = intval(50);
    @endphp
    @forelse ($posts as $index => $post)
        <article class="post post__vertical post__vertical--single post-item"
            style="display: {{ $index < $minMainPostsLimit ? 'flex' : 'none' }}; align-items: center; margin-bottom: 5px;">
            <!-- Image on the left -->
            <div class="post__thumbnail" style="flex: 1.5; width: 48%;">
                {{ RvMedia::image(
                    $post->image,
                    $post->name,
                    'medium',
                    attributes: [
                        'loading' => 'lazy',
                    ],
                ) }}
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
                    <div class="text-dark mb-1">

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
                        Di <span class=" fw-bold author-post" style="color:#8424e3">{{ $post->author->first_name }}
                            {{ $post->author->last_name }}</span> /
                        <a class="fw-bold" href="{{ $post->url }}#comments" style="color:#8424e3">
                            <i class="fa fa-comment" aria-hidden="true"></i>
                            {{ $post->comments_count > 0 ? $post->comments_count : 'Commenta' }}
                        </a>
                    </span>
                </div>

        </article>
    @empty
        <p class="text-center text-muted">
            {{ __('Nessun articolo pubblicato al momento.') }}
        </p>
    @endforelse

    {{-- Botble’s pagination partial --}}
