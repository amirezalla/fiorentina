@php
    use Botble\Blog\Models\Post;
    use Illuminate\Support\Facades\DB;
    use App\Models\Poll;

    $poll = null;
    /*$poll = Poll::with('options')->where('active', true)->latest()->first();
    // Check if the poll exists and has options

    if ($poll) {
        $totalVotes = $poll->options->sum('votes');

        foreach ($poll->options as $option) {
            $option->percentage = $totalVotes > 0 ? round(($option->votes / $totalVotes) * 100) : 0;
        }
    }*/
    $since = Carbon::now()->subDays(5);

    $mostReadPosts = Post::where('created_at', '>=', $since)
        ->orderByDesc('view') // la colonna nel DB è “view”
        ->limit(5)
        ->get();

    /**
     * 2. PIÙ COMMENTATI (recenti + commenti)
     *    – stessa finestra temporale
     *    – conta solo i commenti di tipo Post negli ultimi 30 giorni
     *    – ordina per quel conteggio in modo decrescente
     *
     * NB: se nel modello Post hai già un rapporto `comments()`
     *     puoi usare withCount(); in caso contrario la sub-query qui sotto
     *     funziona senza modificare il modello.
     */
    $mostCommentedPosts = Post::where('posts.created_at', '>=', $since)
        ->leftJoinSub(
            DB::table('fob_comments')
                ->selectRaw('reference_id, COUNT(*) as recent_comment_count')
                ->where('reference_type', Post::class)
                ->where('created_at', '>=', $since)
                ->groupBy('reference_id'),
            'recent_comments',
            'posts.id',
            '=',
            'recent_comments.reference_id',
        )
        ->orderByDesc('recent_comment_count')
        ->limit(5)
        ->get();
@endphp
<meta name="csrf-token" content="{{ csrf_token() }}">

@if ($mostCommentedPosts->isNotEmpty())
    <div class="row mt-30 ad-top-sidebar">
        @include('ads.includes.SIZE_300X250_TOP')
    </div>
    <div class="widget widget__recent-post mt-4 mb-4">
        <ul class="nav nav-tabs" id="postTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <a class="nav-link active" id="recent-posts-tab" data-toggle="tab" href="#recent-posts" role="tab"
                    aria-controls="recent-posts" aria-selected="true">
                    I PIÙ LETTI
                </a>
            </li>
            <li class="nav-item" role="presentation">
                <a class="nav-link" id="most-commented-tab" data-toggle="tab" href="#most-commented" role="tab"
                    aria-controls="most-commented" aria-selected="false">
                    <span style="color: #8424e3; margin-right: 4px;"><i class="fas fa-bolt"></i></span>
                    I PIÙ COMMENTATI
                </a>
            </li>
        </ul>
        <div class="tab-content" id="postTabsContent">
            <div class="tab-pane fade show active" id="recent-posts" role="tabpanel" aria-labelledby="recent-posts-tab">
                <div class="widget__content">
                    <ul>
                        @foreach ($mostReadPosts as $post)
                            <li>
                                <article class="post post__widget d-flex align-items-start"
                                    style="margin-bottom: 10px;">
                                    {{-- Thumbnail on the left, fixed width --}}
                                    <div class="post__thumbnail"
                                        style="width: 80px; flex-shrink: 0; margin-right: 10px;">
                                        {{ RvMedia::image($post->image, $post->name, 'thumb') }}
                                        <a href="{{ $post->url }}" title="{{ $post->name }}"
                                            class="post__overlay"></a>
                                    </div>

                                    {{-- Text content on the right --}}
                                    <header class="post__header" style="flex: 1;">
                                        {{-- Optional: Category label in uppercase, if you want it above the title --}}
                                        @if ($post->categories->count())
                                            <span class="category-span">
                                                {{ strtoupper($post->categories->first()->name) }}
                                            </span>
                                        @endif

                                        {{-- Post Title --}}
                                        <h4 class="post__title" style="margin: 0;">
                                            <a href="{{ $post->url }}" title="{{ $post->name }}"
                                                style="text-decoration: none; color: inherit;">
                                                {{ $post->name }}
                                            </a>
                                        </h4>

                                        {{-- Date --}}
                                        <div class="post__meta date-span"
                                            style="font-size: 0.75rem; color: #999; margin-top: 2px;">
                                            <span class="post__created-at">
                                                {{ Theme::formatDate($post->created_at) }}
                                            </span>
                                        </div>
                                    </header>
                                </article>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
            <div class="tab-pane fade" id="most-commented" role="tabpanel" aria-labelledby="most-commented-tab">
                <div class="widget__content">
                    <ul>
                        @foreach ($mostCommentedPosts as $post)
                            <li>
                                <article class="post post__widget d-flex align-items-start"
                                    style="margin-bottom: 10px;">
                                    {{-- Thumbnail on the left, fixed width --}}
                                    <div class="post__thumbnail"
                                        style="width: 80px; flex-shrink: 0; margin-right: 10px;">
                                        {{ RvMedia::image($post->image, $post->name, 'thumb') }}
                                        <a href="{{ $post->url }}" title="{{ $post->name }}"
                                            class="post__overlay"></a>
                                    </div>

                                    {{-- Text content on the right --}}
                                    <header class="post__header" style="flex: 1;">
                                        {{-- Optional: Category label in uppercase, if you want it above the title --}}
                                        @if ($post->categories->count())
                                            <span
                                                style="display: block; font-size: 0.75rem; text-transform: uppercase; color: #999;">
                                                {{ strtoupper($post->categories->first()->name) }}
                                            </span>
                                        @endif

                                        {{-- Post Title --}}
                                        <h4 class="post__title" style="margin: 0;">
                                            <a href="{{ $post->url }}" title="{{ $post->name }}"
                                                style="text-decoration: none; color: inherit;">
                                                {{ $post->name }}
                                            </a>
                                        </h4>

                                        {{-- Date --}}
                                        <div class="post__meta"
                                            style="font-size: 0.75rem; color: #999; margin-top: 2px;">
                                            <span class="post__created-at">
                                                {{ Theme::formatDate($post->created_at) }}
                                            </span>
                                        </div>
                                    </header>
                                </article>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    </div>
    <div class="row mt-30 ad-top-sidebar">
        @include('ads.includes.SIZE_300X250_B1')
    </div>
    <div class="mt-30">
        @include('videos.includes.adsvideo')
    </div>
    @if ($poll)
        <div class="row">
            <div class="col-12">
                <div>
                    <h1>{{ $poll->question }}</h1>
                    <div id="options-container">
                        @foreach ($poll->options as $option)
                            <div class="row">
                                <button class="col-12 btn btn-outline-primary vote-btn" data-id="{{ $option->id }}"
                                    style="--fill-width: {{ $option->percentage }}%;">
                                    <span
                                        @if ($option->percentage > 16.66) class="option-text-w"

                                    @else
                                        class="option-text-p" @endif>
                                        {{ $option->option }}</span>
                                    <span
                                        @if ($option->percentage < 88) class="percentage-text-p"

                                    @else
                                        class="percentage-text-w" @endif>{{ $totalVotes > 0 ? round(($option->votes / $totalVotes) * 100, 2) : 0 }}
                                        %</span>
                                </button>
                            </div>
                        @endforeach
                    </div>
                    <div id="results-container">
                        @foreach ($poll->options as $option)
                            <div class="result" id="result-{{ $option->id }}">
                                {{ $option->option }}: <span class="percentage">0%</span>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    @endif
    @include('ads.includes.SIZE_300X250_C1')
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            const buttons = document.querySelectorAll('.vote-btn');
            buttons.forEach(button => {
                button.onclick = function() {
                    const optionId = this.getAttribute('data-id');
                    fetch(`/poll-options/${optionId}/vote`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': csrfToken
                            },
                            body: JSON.stringify({
                                // Additional data can be added here if needed
                            })
                        })
                        .then(response => {
                            if (!response.ok) {
                                throw new Error('Network response was not ok: ' + response
                                    .statusText);
                            }
                            return response.json();
                        })
                        .then(data => {
                            updateResults(data.results, optionId);
                        })
                        .catch(error => console.error('Error:', error));
                    this.disabled = true; // Disable the button after vote
                };
            });
        });

        function updateResults(results, votedOptionId) {
            results.forEach(result => {
                const button = document.querySelector(`.vote-btn[data-id="${result.id}"]`);
                if (button) {
                    const percentage = result.percentage;
                    const optionText = result.option;

                    // Update button width according to the new percentage
                    button.style.setProperty('--fill-width', `${percentage}%`);
                    button.querySelector('.percentage-text').textContent = `${percentage}%`;

                    // Optionally disable other buttons after voting
                    if (result.id.toString() !== votedOptionId) {
                        button.disabled = true;
                    }
                }
            });
        }
    </script>

    <style>
        .btn-purple {
            background-color: purple;
            color: white;
        }
    </style>


@endif
