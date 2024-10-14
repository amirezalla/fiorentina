@php
    use App\Models\Poll;

    $poll = Poll::with('options')->where('active', true)->latest()->first();
    // Check if the poll exists and has options

    if ($poll) {
        $totalVotes = $poll->options->sum('votes');

        foreach ($poll->options as $option) {
            $option->percentage = $totalVotes > 0 ? round(($option->votes / $totalVotes) * 100) : 0;
        }
    }

@endphp
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
                    <div class="post-group post-group--single">
                        <div class="post-group__header">
                            <h3 class="post-group__title">ULTIME NOTIZIE</h3>
                        </div>
                        <div class="post-group__content">
                            <div class="row">
                                <div class="col-md-12 col-sm-12 col-12">
                                    @foreach ($posts as $index => $post)
                                        <article class="post post__vertical post__vertical--single post-item"
                                            style="display: flex; align-items: center; margin-bottom: 5px; {{ $index >= 6 ? 'display: none;' : '' }}">
                                            <!-- Image on the left -->
                                            <div class="post__thumbnail" style="flex: 1.5; width: 48%;">
                                                {{ RvMedia::image($post->image, $post->name, 'large') }}
                                                <a class="post__overlay" href="{{ $post->url }}"
                                                    title="{{ $post->name }}"></a>
                                            </div>

                                            <!-- Content (Title and Description) on the right -->
                                            <div class="post__content-wrap" style="flex: 2.5; padding-left: 20px;">
                                                <header class="post__header">
                                                    <h4 class="post__title" style="margin: 0;">
                                                        <a href="{{ $post->url }}" title="{{ $post->name }}"
                                                            style="text-decoration: none; color: inherit;">
                                                            {{ $post->name }}
                                                        </a>
                                                    </h4>
                                                </header>
                                                <div class="post__content">
                                                    <p style="margin: 10px 0 0;">{{ $post->description }}</p>
                                                </div>
                                            </div>
                                        </article>
                                    @endforeach
                                        @include('ads.includes.adsrecentp1')

                                    <!-- Load More Button -->
                                    @if (count($posts) > 6)
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
                                {{--                                        <div class="col-md-6 col-sm-6 col-12"> --}}
                                {{--                                            <article class="post post__vertical post__vertical--single"> --}}
                                {{--                                                <div class="post__thumbnail"> --}}
                                {{--                                                    {{ RvMedia::image($post->image, $post->name, 'medium') }} --}}
                                {{--                                                    <a class="post__overlay" href="{{ $post->url }}" --}}
                                {{--                                                        title="{{ $post->name }}"></a> --}}
                                {{--                                                </div> --}}
                                {{--                                                <div class="post__content-wrap"> --}}
                                {{--                                                    <header class="post__header"> --}}
                                {{--                                                        <h3 class="post__title"><a href="{{ $post->url }}" --}}
                                {{--                                                                title="{{ $post->name }}">{{ $post->name }}</a></h3> --}}
                                {{--                                                        <div class="post__meta"><span --}}
                                {{--                                                                class="created__month">{{ $post->created_at->translatedFormat('M') }}</span><span --}}
                                {{--                                                                class="created__date">{{ $post->created_at->translatedFormat('d') }}</span><span --}}
                                {{--                                                                class="created__year">{{ $post->created_at->translatedFormat('Y') }}</span> --}}
                                {{--                                                        </div> --}}
                                {{--                                                    </header> --}}
                                {{--                                                    <div class="post__content"> --}}
                                {{--                                                        <p data-number-line="4">{{ $post->description }}</p> --}}
                                {{--                                                    </div> --}}
                                {{--                                                </div> --}}
                                {{--                                            </article> --}}
                                {{--                                        </div> --}}
                                {{--                                        <div class="col-md-6 col-sm-6 col-12"> --}}
                                {{--                                        @else --}}
                                {{--                                            <article --}}
                                {{--                                                class="post post__horizontal post__horizontal--single mb-20 clearfix"> --}}
                                {{--                                                <div class="post__thumbnail"> --}}
                                {{--                                                    {{ RvMedia::image($post->image, $post->name, 'medium') }} --}}
                                {{--                                                    <a class="post__overlay" href="{{ $post->url }}" --}}
                                {{--                                                        title="{{ $post->name }}"></a> --}}
                                {{--                                                </div> --}}
                                {{--                                                <div class="post__content-wrap"> --}}
                                {{--                                                    <header class="post__header"> --}}
                                {{--                                                        <h3 class="post__title"><a href="{{ $post->url }}" --}}
                                {{--                                                                title="{{ $post->name }}">{{ $post->name }}</a> --}}
                                {{--                                                        </h3> --}}
                                {{--                                                        <div class="post__meta"><span --}}
                                {{--                                                                class="post__created-at">{{ Theme::formatDate($post->created_at) }}</span> --}}
                                {{--                                                        </div> --}}
                                {{--                                                    </header> --}}
                                {{--                                                </div> --}}
                                {{--                                            </article> --}}
                                {{--                                    @endif --}}
                                {{--                                    @if ($loop->last) --}}
                                {{--                            </div> --}}
                                {{--                            @endif --}}

                            </div>
                        </div>
                    </div>
                </div>
            </div>

            @if ($topSidebarContent)

                <div class="col-lg-4">

                    <div class="page-sidebar">
                        <section>
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
                                                $labelClass = 'text-dark'; // Top 6
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
                            @include('videos.includes.adsvideo')
{{--                            @include('videos.includes.adsvideo', ['videos' => $videos])--}}
                        </section>
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
    document.getElementById('load-more').addEventListener('click', function() {
        const hiddenArticles = document.querySelectorAll('article.post-item[style*="display: none;"]');

        hiddenArticles.forEach(article => {
            article.style.display = 'flex';
        });

        // Hide the Load More button after revealing all hidden articles
        this.style.display = 'none';
    });
</script>
