@php
    use Botble\Blog\Models\Post;
    use Illuminate\Support\Facades\DB;
    use App\Models\Poll;

    $poll = Poll::with('options')->where('active', true)->latest()->first();
    // Check if the poll exists and has options

    if ($poll) {
        $totalVotes = $poll->options->sum('votes');

        foreach ($poll->options as $option) {
            $option->percentage = $totalVotes > 0 ? round(($option->votes / $totalVotes) * 100) : 0;
        }
    }
    $recentPosts = Post::orderBy('created_at', 'desc')->limit(5)->get();

    $mostCommentedPosts = DB::select("
    SELECT posts.*
    FROM posts
    JOIN (
        SELECT reference_id, COUNT(reference_id) as comment_count
        FROM fob_comments
        WHERE reference_type = 'Botble\\\\Blog\\\\Models\\\\Post'
        GROUP BY reference_id
        ORDER BY comment_count DESC
        LIMIT 5
    ) as most_commented
    ON posts.id = most_commented.reference_id;
");

    // If you need to convert the result into a collection of Post models, you can do this:
    $mostCommentedPosts = collect($mostCommentedPosts)->map(function ($post) {
        return (new \Botble\Blog\Models\Post())->newFromBuilder($post);
    });
@endphp
<meta name="csrf-token" content="{{ csrf_token() }}">

@if ($mostCommentedPosts->isNotEmpty())
    <div class="row mt-30 ad-top-sidebar">
        @include('ads.includes.SIZE_300X250_C1')
    </div>
    <div class="widget widget__recent-post mt-4 mb-4">
        <ul class="nav nav-tabs" id="postTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <a class="nav-link active" id="recent-posts-tab" data-toggle="tab" href="#recent-posts" role="tab"
                    aria-controls="recent-posts" aria-selected="true">I PIÙ RECENTI</a>
            </li>
            <li class="nav-item" role="presentation">
                <a class="nav-link" id="most-commented-tab" data-toggle="tab" href="#most-commented" role="tab"
                    aria-controls="most-commented" aria-selected="false">I PIÙ COMMENTATI</a>
            </li>
        </ul>
        <div class="tab-content" id="postTabsContent">
            <div class="tab-pane fade show active" id="recent-posts" role="tabpanel" aria-labelledby="recent-posts-tab">
                <div class="widget__content">
                    <ul>
                        @foreach ($recentPosts as $post)
                            <li>
                                <article class="post post__widget clearfix">
                                    <div class="post__thumbnail">
                                        {{ RvMedia::image($post->image, $post->name, 'thumb') }}
                                        <a href="{{ $post->url }}" title="{{ $post->name }}"
                                            class="post__overlay"></a>
                                    </div>
                                    <header class="post__header">
                                        <h4 class="post__title text-truncate-2"><a href="{{ $post->url }}"
                                                title="{{ $post->name }}" data-number-line="2">{{ $post->name }}</a>
                                        </h4>
                                        <div class="post__meta"><span
                                                class="post__created-at">{{ Theme::formatDate($post->created_at) }}</span>
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
                                <article class="post post__widget clearfix">
                                    <div class="post__thumbnail">
                                        {{ RvMedia::image($post->image, $post->name, 'thumb') }}
                                        <a href="{{ $post->url }}" title="{{ $post->name }}"
                                            class="post__overlay"></a>
                                    </div>
                                    <header class="post__header">
                                        <h4 class="post__title text-truncate-2"><a href="{{ $post->url }}"
                                                title="{{ $post->name }}"
                                                data-number-line="2">{{ $post->name }}</a></h4>
                                        <div class="post__meta"><span
                                                class="post__created-at">{{ Theme::formatDate($post->created_at) }}</span>
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
        @include('ads.includes.SIZE_300X250_TOP')
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
