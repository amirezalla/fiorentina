@php
    use Botble\Blog\Models\Post;
    use Illuminate\Support\Facades\DB;
    use App\Models\Poll;
    use Carbon\Carbon;

    $poll = null;
    /*$poll = Poll::with('options')->where('active', true)->latest()->first();
    // Check if the poll exists and has options

    if ($poll) {
        $totalVotes = $poll->options->sum('votes');

        foreach ($poll->options as $option) {
            $option->percentage = $totalVotes > 0 ? round(($option->votes / $totalVotes) * 100) : 0;
        }
    }*/
    $since = Carbon::now()->subDays(600);

    $mostReadPosts = Post::where('created_at', '>=', $since)
        ->orderByDesc('views') // la colonna nel DB è “view”
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
    /*  NEW ➜ 15 post più recenti  */
    $mostRecentPosts = Post::orderByDesc('created_at')->limit(15)->get();
@endphp
<meta name="csrf-token" content="{{ csrf_token() }}">
<style>
    .slider-viewport {
        width: 100%;
    }

    .slider-track {
        transition: transform .4s ease;
    }

    .slider-track ul {
        padding: 0 5px;
    }

    /* BIGGER ARROWS */
    .slider-btn {
        width: 42px;
        /* ⇠ larger hit-area */
        height: 42px;
        font-size: 1.4rem;
        /* ⇠ bigger ‹ › glyphs */
        border-radius: 50%;
        /* round buttons */
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 2px 4px rgba(0, 0, 0, .15);
        z-index: 2;
        /* stay above slides */
    }

    /* DOTS */
    .slider-dot {

        height: 5px;

        background: #ccc;
        border: none;
        margin: 0 4px;
        cursor: pointer;
        transition: background .2s;
    }

    .slider-dot.active {
        background: #8424e3;
        /* theme color */
    }

    .btn-purple {
        background-color: purple;
        color: white;
    }

    /* fixed height & scroll */
    .recent-scroll {
        max-height: 480px;
        /* tweak to taste */
        overflow-y: auto;
    }

    /* prettier scrollbar — WebKit & Firefox */
    .recent-scroll {
        scrollbar-width: thin;
        scrollbar-color: #00d26a transparent;
        /* thumb / track */
    }

    .recent-scroll::-webkit-scrollbar {
        width: 6px;
    }

    .recent-scroll::-webkit-scrollbar-thumb {
        background: #00d26a;
        border-radius: 3px;
    }

    .recent-scroll::-webkit-scrollbar-track {
        background: transparent;
    }

    .recent-scroll:hover::-webkit-scrollbar-thumb {
        background: #00b85a;
    }



    /* a little gutter */
</style>

@if ($mostCommentedPosts->isNotEmpty())
    <div class="row mt-30 ad-top-sidebar">
        @include('ads.includes.SIZE_300X250_TOP')
    </div>

    <div class="widget__content position-relative" id="most-recent">
        <div class="editoriale-item-head d-flex" style="border-bottom: 2px solid #ccc;margin: 25px 0px 12px 0px;">
            <span class="editoriale-item-head-title px-1 text-dark heading-container"
                style="border-bottom: 2px solid #8424e3;margin-bottom:-2px;font-weight: 700;">
                {{-- The title of the section --}}
                I PIÙ RECENTI
            </span>
        </div>
        <!-- arrows -->
        <button class="slider-btn slider-prev btn btn-sm btn-light position-absolute"
            style="left:-10px;top:50%;transform:translateY(-50%);" disabled>&lsaquo;</button>
        <button class="slider-btn slider-next btn btn-sm btn-light position-absolute"
            style="right:-10px;top:50%;transform:translateY(-50%);">&rsaquo;</button>

        <!-- viewport -->
        <div class="slider-viewport overflow-hidden">
            <!-- track width = (#slides × 100 %)  -->
            <div class="slider-track d-flex transition"
                style="width: {{ ceil($mostRecentPosts->count() / 5) * 100 }}%;">
                @foreach ($mostRecentPosts->chunk(5) as $chunk)
                    <!-- each UL = one slide, width = 100 / #slides %  -->
                    <ul class="list-unstyled m-0 p-0 d-flex flex-column"
                        style="width: {{ 100 / ceil($mostRecentPosts->count() / 5) }}%;">
                        @foreach ($chunk as $post)
                            <li class="mb-2">
                                <article class="post post__widget d-flex align-items-start">
                                    <div class="post__thumbnail" style="width:80px;flex-shrink:0;margin-right:10px;">
                                        {{ RvMedia::image($post->image, $post->name, 'thumb') }}
                                        <a href="{{ $post->url }}" class="post__overlay"
                                            title="{{ $post->name }}"></a>
                                    </div>

                                    <header class="post__header" style="flex:1;">
                                        @if ($post->categories->count())
                                            <span style="font-size:.75rem;text-transform:uppercase;color:#999;">
                                                {{ strtoupper($post->categories->first()->name) }}
                                            </span>
                                        @endif

                                        <h4 class="post__title" style="margin:0;">
                                            <a href="{{ $post->url }}" style="text-decoration:none;color:inherit;">
                                                {{ $post->name }}
                                            </a>
                                        </h4>

                                        <div class="post__meta" style="font-size:.75rem;color:#999;margin-top:2px;">
                                            <span class="post__created-at">
                                                {{ Theme::formatDate($post->created_at) }}
                                            </span>
                                        </div>
                                    </header>
                                </article>
                            </li>
                        @endforeach
                    </ul>
                @endforeach
            </div>
        </div>
        <div class="slider-dots d-flex justify-content-center mb-2"></div>
    </div>



    <div class="widget__content" id="most-recent1">
        {{-- header ------------------------------------------------------------ --}}
        <div class="editoriale-item-head d-flex mb-3" style="border-bottom:2px solid #ccc;">
            <span class="editoriale-item-head-title px-1 text-dark"
                style="border-bottom:2px solid #8424e3;font-weight:700;">
                I PIÙ RECENTI
            </span>
        </div>

        {{-- SCROLLABLE LIST  --------------------------------------------------- --}}
        <div class="recent-scroll pe-1"> {{-- 👈 new wrapper --}}
            <ul class="list-unstyled m-0">
                @foreach ($mostRecentPosts as $post)
                    <li class="py-3 border-bottom">
                        <article class="d-flex align-items-start">
                            <div style="width:80px;flex-shrink:0;margin-right:10px;">
                                {{ RvMedia::image($post->image, $post->name, 'thumb') }}
                                <a href="{{ $post->url }}" class="post__overlay" title="{{ $post->name }}"></a>
                            </div>

                            <header style="flex:1;">
                                @if ($post->categories->count())
                                    <span style="font-size:.7rem;text-transform:uppercase;color:#999;">
                                        {{ strtoupper($post->categories->first()->name) }}
                                    </span>
                                @endif

                                <h4 style="margin:0;font-size:.95rem;line-height:1.2;">
                                    <a href="{{ $post->url }}" class="text-dark text-decoration-none">
                                        {{ $post->name }}
                                    </a>
                                </h4>

                                <div style="font-size:.7rem;color:#999;margin-top:2px;">
                                    {{ Theme::formatDate($post->created_at) }}
                                </div>
                            </header>
                        </article>
                    </li>
                @endforeach
            </ul>
        </div>

        {{-- optional “More news” footer --------------------------------------- --}}
        <div class="text-center py-3">
            <a href="" class="fw-semibold text-dark text-decoration-none">
                Più notizie <span>&#10140;</span>
            </a>
        </div>
    </div>


    <div class="row mt-30 ad-top-sidebar">
        @include('ads.includes.SIZE_300X250_B1')
    </div>

    <div class="widget widget__recent-post mt-4 mb-4">
        <ul class="nav nav-tabs" id="postTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <a class="nav-link active" id="recent-posts-tab" data-toggle="tab" href="#recent-posts" role="tab"
                    aria-controls="recent-posts" aria-selected="false">
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

            <div class="tab-pane show active" id="recent-posts" role="tabpanel" aria-labelledby="recent-posts-tab">
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

        document.addEventListener('DOMContentLoaded', () => {
            const pane = document.querySelector('#most-recent');
            if (!pane) return;

            const track = pane.querySelector('.slider-track');
            const slides = pane.querySelectorAll('.slider-track > ul');
            const prevBtn = pane.querySelector('.slider-prev');
            const nextBtn = pane.querySelector('.slider-next');
            const dotsBox = pane.querySelector('.slider-dots'); // NEW

            let index = 0;
            const total = slides.length;
            const slidePct = 100 / total;

            /* NEW ➜ create dots */
            const dots = [...Array(total)].map((_, i) => {
                const btn = document.createElement('button');
                btn.className = 'slider-dot';
                btn.addEventListener('click', () => {
                    index = i;
                    update();
                });
                dotsBox.appendChild(btn);
                return btn;
            });

            const update = () => {
                track.style.transform = `translateX(-${index * slidePct}%)`;
                prevBtn.disabled = index === 0;
                nextBtn.disabled = index === total - 1;

                /* NEW ➜ toggle active color */
                dots.forEach((d, i) => d.classList.toggle('active', i === index));
            };

            prevBtn.addEventListener('click', () => {
                if (index) {
                    index--;
                    update();
                }
            });
            nextBtn.addEventListener('click', () => {
                if (index < total - 1) {
                    index++;
                    update();
                }
            });

            update(); // first paint
        });
    </script>




@endif
