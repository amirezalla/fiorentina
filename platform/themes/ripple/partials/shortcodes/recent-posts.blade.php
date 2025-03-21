@php
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
@endphp

<div></div>

<div class="d-block d-md-none col-12 text-center">
    @include('ads.includes.MOBILE_HOME_HERO_25')
</div>

<section class="section recent-posts pt-20 pb-20"
    @if ($shortcode->background_color) style="background-color: {{ $shortcode->background_color }} !important;" @endif>
    <div class="container bg-white">
        <div class="row">
            @php
                $topSidebarContent = $withSidebar ? dynamic_sidebar('top_sidebar') : null;

            @endphp
            <div @class([
                'col-lg-8' => $topSidebarContent,
                'col-12' => !$topSidebarContent,
            ])>
                <div class="page-content">
                    <div class="heading-container">
                        <h2 class="heading-partial-underline">ULTIME NOTIZIE</h2>
                    </div>
                    <div class="post-group post-group--single">
                        <div class="post-group__header">
                            <div class="row"></div>
                        </div>
                        <div class="post-group__content">
                            <div class="row">
                                @php
                                    $minMainPostsLimit = intval(setting('min_main_posts_limit'));
                                    $mainPostsLimit = intval(setting('main_posts_limit', 12));
                                @endphp
                                <div class="col-md-12 col-sm-12 col-12">
                                    @foreach ($posts as $index => $post)
                                        <article class="post post__vertical post__vertical--single post-item"
                                            style="display: {{ $index < $minMainPostsLimit ? 'flex' : 'none' }}; align-items: center; margin-bottom: 5px;">

                                            <!-- Image on the left -->
                                            <div class="post__thumbnail" style="flex: 1.5; width: 48%;">
                                                {{ RvMedia::image($post->image, $post->name, 'large') }}
                                                <a class="post__overlay" href="{{ $post->url }}"
                                                    title="{{ $post->name }}"></a>
                                            </div>

                                            <!-- Content on the right -->
                                            <div class="post__content-wrap" style="flex: 2.5; padding-left: 20px;">
                                                <header class="post__header">

                                                    {{-- 1. CATEGORY LABEL (e.g. "RASSEGNA STAMPA" or "NOTIZIE") --}}
                                                    @php
                                                        $categoryName = $post->categories->count()
                                                            ? strtoupper($post->categories->first()->name)
                                                            : 'NEWS';
                                                    @endphp
                                                    <span
                                                        style="display: block; font-size: 0.8rem; text-transform: uppercase; color: #999; margin-bottom: 4px;">
                                                        {{ $categoryName }}
                                                    </span>

                                                    {{-- 2. POST TITLE --}}
                                                    <h4 class="post__title" style="margin: 0;">
                                                        <a href="{{ $post->url }}" title="{{ $post->name }}"
                                                            style="text-decoration: none; color: inherit;">
                                                            {{ $post->name }}
                                                        </a>
                                                    </h4>

                                                    {{-- 3. DATE + "IN AGGIORNAMENTO" BADGE --}}
                                                    @php
                                                        $date = $post->created_at;
                                                        if ($date->isToday()) {
                                                            $formattedDate = $date->format('H:i');
                                                        } elseif ($date->isYesterday()) {
                                                            $formattedDate = 'Ieri alle ' . $date->format('H:i');
                                                        } else {
                                                            $formattedDate = $date
                                                                ->locale('it')
                                                                ->translatedFormat('d M H:i');
                                                        }
                                                    @endphp

                                                    <div class="text-dark" style="margin-top: 4px;">
                                                        <span class="post__date">{{ $formattedDate }}</span>
                                                        @if ($post->in_aggiornamento)
                                                            <span class="post-group__left-red-badge mb-2 ml-2">
                                                                <i class="fa fa-spinner text-white"></i>
                                                                <span class="text-white">In Aggiornamento</span>
                                                            </span>
                                                        @endif
                                                    </div>
                                                </header>

                                                {{-- 4. EXCERPT / DESCRIPTION --}}
                                                <div class="post__content">
                                                    <p style="margin: 10px 0 0;">{{ $post->description }}</p>

                                                    {{-- 5. AUTHOR + COMMENTS --}}
                                                    <span class="text-dark mt-3 d-block">
                                                        @php
                                                            // Count comments
                                                            $post->comments_count = FriendsOfBotble\Comment\Models\Comment::where(
                                                                'reference_id',
                                                                $post->id,
                                                            )->count();
                                                        @endphp
                                                        Di
                                                        <span class="fw-bold author-post" style="color: #8424e3;">
                                                            {{ $post->author->first_name }}
                                                            {{ $post->author->last_name }}
                                                        </span>
                                                        /
                                                        <a class="fw-bold" href="{{ $post->url }}#comments"
                                                            style="color: #8424e3;">
                                                            <i class="fa fa-comment" aria-hidden="true"></i>
                                                            {{ $post->comments_count > 0 ? $post->comments_count : 'Commenta' }}
                                                        </a>
                                                    </span>
                                                </div>
                                            </div>
                                        </article>

                                        <!-- Optional ads -->
                                        @if ($index == 0)
                                            <div class="d-none d-md-block">
                                                @include('ads.includes.adsrecentp1')

                                            </div>
                                            <div class="d-block d-md-none col-12 mb-4 text-center">
                                                @include('ads.includes.MOBILE_POSIZIONE_1')
                                            </div>
                                        @endif
                                        @if ($index == 2)
                                            <div class="d-none d-md-block">
                                                @include('ads.includes.adsrecentp2')
                                            </div>

                                            <div class="d-block d-md-none col-12 mb-4 text-center">
                                                @include('ads.includes.MOBILE_POSIZIONE_2')
                                            </div>
                                        @endif
                                        @if ($index == 5)
                                            <div class="d-none d-md-block">
                                                @include('ads.includes.adsrecentp3')
                                            </div>

                                            <div class="d-block d-md-none col-12 mb-4 text-center">
                                                @include('ads.includes.MOBILE_POSIZIONE_3')
                                            </div>
                                        @endif
                                        @if ($index == 7)
                                            <div class="d-none d-md-block">
                                                @include('ads.includes.adsrecentp2')
                                            </div>

                                            <div class="d-block d-md-none col-12 mb-4 text-center">
                                                @include('ads.includes.MOBILE_POSIZIONE_4')
                                            </div>
                                        @endif
                                        @if ($index == 10)
                                            <div class="d-none d-md-block">
                                                @include('ads.includes.adsrecentp1')

                                            </div>
                                            <div class="d-block d-md-none col-12 mb-4 text-center">
                                                @include('ads.includes.MOBILE_POSIZIONE_5')
                                            </div>
                                        @endif
                                    @endforeach

                                    <!-- Load More Button -->
                                    @if ($postsCount > $minMainPostsLimit)
                                        <div style="text-align: center;">
                                            <button id="load-more"
                                                style="
                                                    background: #fff;
                                                    border: 2px solid #aaa;
                                                    border-radius: 3px;
                                                    display: inline-block;
                                                    font-size: .8rem;
                                                    font-weight: 600;
                                                    letter-spacing: .02em;
                                                    line-height: 1;
                                                    margin-top: 20px;
                                                    margin-bottom: 20px;
                                                    padding: 15px 0;
                                                    text-align: center;
                                                    text-transform: uppercase;
                                                    width: 88.4%;
                                                    cursor: pointer;
                                                    color: #441274; /* Violet text color */
                                                    transition: border-color 0.3s ease;
                                                "
                                                onmouseover="this.style.borderColor='#441274';"
                                                onmouseout="this.style.borderColor='#aaa';">
                                                ALTRE NOTIZIE
                                            </button>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>


            @if ($topSidebarContent)
                @php
                    $match = App\Models\Calendario::where('status', 'SCHEDULED')
                        ->orWhere('status', 'LIVE')
                        ->orderBy('match_date', 'asc')
                        ->first();
                    $home_team = json_decode($match->home_team, true);
                    $away_team = json_decode($match->away_team, true);

                @endphp
                <div class="col-lg-4">
                    <div class="page-sidebar">
                        <section>
                            <div class="row align-items-center upcoming-match upcoming-match-sidebar">
                                <!-- Match Date, Time, and Venue -->
                                <div class="col-md-12 text-center">
                                    <p>{{ ucwords(\Carbon\Carbon::parse($match->match_date)->locale('it')->timezone('Europe/Rome')->isoFormat('dddd D MMMM [ore] H:mm'), " \t\r\n\f\v") }}
                                    </p>
                                </div>

                                <!-- Team Logos and Names -->
                                <div class="col-md-12 text-center">
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
                                <div class="col-md-12 text-center">
                                    @if ($match->status == 'LIVE')
                                        <button class="btn btn-primary">VAI ALLA DIRETTA</button>
                                    @else
                                        <div id="countdown mt-10"
                                            style="background: #441274;padding:10px;border-radius:3px;">
                                            <i class="fa fa-clock-o" aria-hidden="true"></i> <span
                                                id="countdown-timer"></span>
                                        </div>
                                    @endif
                                </div>
                            </div>
                    </div>
                    <div class="row mt-30 ad-top-sidebar">
                        @include('ads.includes.SIZE_300X250_TOP')
                    </div>
                    @include('last_post_editoriale')
                    <div class="page-content">
                        <div class="post-group">
                            <div class="post-group__header">
                                <h3 class="post-group__title">CLASSIFICA SERIE A</h3>
                            </div>
                        </div>
                    </div>


                    <div>
                        @php
                            $updateMessage = App\Http\Controllers\StandingController::fetchStandingsIfNeeded();
                            $updateScheduledMessage = App\Http\Controllers\StandingController::fetchScheduledMatches();
                        @endphp
                    </div>
                    <table class="table table-sm table-striped">
                        <thead
                            style="
                            background: blueviolet;
                            border: 1px solid white;
                            color: white;
                            font-weight: 900;
                        ">
                            <tr>
                                <th style="border-right: 1px solid white;"></th>
                                <th style="border-right: 1px solid white;">PT</th>
                                <th style="border-right: 1px solid white;">G</th>
                                <th style="border-right: 1px solid white;">V</th>
                                <th style="border-right: 1px solid white;">N</th>
                                <th style="border-right: 1px solid white;">P</th>
                                <th>DR</th>
                            </tr>
                        </thead>
                        <tbody
                            style="
                            background: white;
                            border: 1px solid white;
                        ">

                            @foreach (App\Models\Standing::all() as $index => $standing)
                                @php
                                    // Assign special styles or labels based on the position
                                    $rank = $index + 1;
                                    $labelClass = '';
                                    if ($rank <= 4) {
                                        $labelClass = 'badge badge-success'; // First place
                                    } elseif ($rank == 5) {
                                        $labelClass = 'badge badge-warning'; // Top 4
                                    } elseif ($rank == 6) {
                                        $labelClass = 'badge badge-warning'; // Top 6
                                    } elseif ($rank >= 18) {
                                        $labelClass = 'badge badge-danger'; // Top 6
                                    } else {
                                        $labelClass = 'text-dark badge badge-light'; // Top 6
                                    }
                                @endphp

                                <tr style="border-bottom:1px solid blueviolet">
                                    <td @if ($standing->short_name == 'Fiorentina') style="background-color:#8a2be270 !important;" @endif
                                        style="border-right: 1px solid blueviolet;">
                                        <span class="{{ $labelClass }}">{{ $rank }}</span>
                                        <img src="{{ $standing->crest_url }}" width="15">
                                        {{ $standing->short_name }}
                                    </td>
                                    <td @if ($standing->short_name == 'Fiorentina') style="background-color:#8a2be270 !important;" @endif
                                        style="border-right: 1px solid blueviolet;">
                                        {{ $standing->points }}
                                    </td>
                                    <td @if ($standing->short_name == 'Fiorentina') style="background-color:#8a2be270 !important;text-align:center" @endif
                                        style="border-right: 1px solid blueviolet;text-align:center">
                                        {{ $standing->played_games }}
                                    </td>
                                    <td @if ($standing->short_name == 'Fiorentina') style="background-color:#8a2be270 !important;text-align:center" @endif
                                        style="border-right: 1px solid blueviolet;text-align:center">
                                        {{ $standing->won }}
                                    </td>
                                    <td @if ($standing->short_name == 'Fiorentina') style="background-color:#8a2be270 !important;text-align:center" @endif
                                        style="border-right: 1px solid blueviolet;text-align:center">
                                        {{ $standing->draw }}
                                    </td>
                                    <td @if ($standing->short_name == 'Fiorentina') style="background-color:#8a2be270 !important;text-align:center" @endif
                                        style="border-right: 1px solid blueviolet;text-align:center">
                                        {{ $standing->lost }}
                                    </td>
                                    <td @if ($standing->short_name == 'Fiorentina') style="background-color:#8a2be270 !important;text-align:center" @endif
                                        style="text-align:center">{{ $standing->goal_difference }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    <div class="legend mb-4">
                        <span class="badge badge-success"
                            style="display: inline-block; width: 15px; height: 15px; margin-right: 5px;"></span>
                        Champions League
                        <span class="badge badge-warning"
                            style="display: inline-block; width: 15px; height: 15px; margin-right: 5px;"></span>
                        Europa & Conference League
                        <br>
                        <span class="badge badge-danger"
                            style="display: inline-block; width: 15px; height: 15px; margin-right: 5px;"></span>
                        Serie B
                    </div>
                    @include('videos.includes.adsvideo', ['foo' => 'bar'])

                    <div class="row mt-30 ad-top-sidebar">
                        @include('ads.includes.SIZE_300X250_C1')
                    </div>

                    @if ($poll)
                        <div class="row container mt-4">
                            <div class="col-12">
                                <div>
                                    <h1>{{ $poll->question }}</h1>
                                    <div id="options-container">
                                        @foreach ($poll->options as $option)
                                            <div class="row">
                                                <button class="col-12 btn btn-outline-primary vote-btn"
                                                    data-id="{{ $option->id }}"
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
</section>
</div>

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

</div>
@endif





</div>
</div>
</section>

{{-- @include('ads.includes.adsense', ['adClient' => 'ca-pub-6741446998584415', 'adSlot' => 'YYYYYYYYYY']) --}}
@include('ads.includes.adsense', ['adClient' => 'ca-pub-6741446998584415'])

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const loadMoreButton = document.getElementById('load-more');
        let visiblePosts = parseInt('{{ setting('min_main_posts_limit') }}');
        const mainPostsLimit = parseInt('{{ setting('main_posts_limit', 20) }}');
        const minMainPostsLimit = parseInt('{{ setting('min_main_posts_limit') }}');

        if (loadMoreButton) {
            loadMoreButton.addEventListener('click', function() {
                const allPosts = document.querySelectorAll('.post-item');

                if (loadMoreButton.innerText === 'ALTRE NOTIZIE') {
                    // Expand posts up to mainPostsLimit
                    visiblePosts = Math.min(visiblePosts + mainPostsLimit, allPosts.length);

                    allPosts.forEach((post, index) => {
                        if (index < visiblePosts) {
                            post.style.display = 'flex';
                        }
                    });

                    if (visiblePosts >= allPosts.length) {
                        loadMoreButton.innerText = 'MOSTRA MENO';
                    }
                } else {
                    // Collapse posts back to minMainPostsLimit
                    visiblePosts = minMainPostsLimit;

                    allPosts.forEach((post, index) => {
                        post.style.display = index < visiblePosts ? 'flex' : 'none';
                    });

                    loadMoreButton.innerText = 'ALTRE NOTIZIE';
                }
            });
        }
    });
</script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        @if ($match->status != 'LIVE')
            // Set the date we're counting down to
            var countDownDate = new Date(
                "{{ \Carbon\Carbon::parse($match->match_date)->timezone('Europe/Rome')->toIso8601String() }}"
            ).getTime();

            // Update the count down every 1 second
            var countdownFunction = setInterval(function() {
                // Get today's date and time
                var now = new Date().getTime();

                // Find the distance between now and the count down date
                var distance = countDownDate - now;

                // Time calculations for days, hours, minutes and seconds
                var days = Math.floor(distance / (1000 * 60 * 60 * 24));
                var hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                var minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
                var seconds = Math.floor((distance % (1000 * 60)) / 1000);

                // Build the countdown string
                var countdownString = "tra ";
                if (days > 0) {
                    countdownString += days + " giorn" + (days != 1 ? "i" : "o") + " ";
                }
                if (hours > 0) {
                    countdownString += hours + " or" + (hours != 1 ? "e" : "a") + " ";
                }
                if (minutes > 0) {
                    countdownString += minutes + " minut" + (minutes != 1 ? "i" : "o") + " ";
                }


                // Display the result in the element with id="countdown-timer"
                document.getElementById("countdown-timer").innerHTML = countdownString.trim();

                // If the count down is over, write some text
                if (distance < 0) {
                    clearInterval(countdownFunction);
                    document.getElementById("countdown-timer").innerHTML = "MATCH STARTED";
                }
            }, 1000);
        @endif
    });
</script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        @if ($match->status != 'LIVE')
            // Set the date we're counting down to
            var countDownDate = new Date(
                "{{ \Carbon\Carbon::parse($match->match_date)->timezone('Europe/Rome')->toIso8601String() }}"
            ).getTime();

            // Update the count down every 1 second
            var countdownFunction = setInterval(function() {
                // Get today's date and time
                var now = new Date().getTime();

                // Find the distance between now and the count down date
                var distance = countDownDate - now;

                // Time calculations for days, hours, minutes and seconds
                var days = Math.floor(distance / (1000 * 60 * 60 * 24));
                var hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                var minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
                var seconds = Math.floor((distance % (1000 * 60)) / 1000);

                // Build the countdown string
                var countdownString = "tra ";
                if (days > 0) {
                    countdownString += days + " giorn" + (days != 1 ? "i" : "o") + " ";
                }
                if (hours > 0) {
                    countdownString += hours + " or" + (hours != 1 ? "e" : "a") + " ";
                }
                if (minutes > 0) {
                    countdownString += minutes + " minut" + (minutes != 1 ? "i" : "o") + " ";
                }


                // Display the result in the element with id="countdown-timer"
                document.getElementById("countdown-timer1").innerHTML = countdownString.trim();

                // If the count down is over, write some text
                if (distance < 0) {
                    clearInterval(countdownFunction);
                    document.getElementById("countdown-timer1").innerHTML = "MATCH STARTED";
                }
            }, 1000);
        @endif
    });
</script>
