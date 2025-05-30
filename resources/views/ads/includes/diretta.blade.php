{{-- Live One here  --}}
@php
    use App\Models\Calendario;
    use App\Models\MatchLineups;
    use App\Models\MatchStatics;
    use App\Models\MatchCommentary;
    use Illuminate\Support\Facades\DB;
    use App\Models\MatchSummary;
    use App\Http\Controllers\MatchLineupsController;
    use App\Http\Controllers\MatchStaticsController;
    use App\Http\Controllers\MatchCommentaryController;
    use App\Http\Controllers\MatchSummaryController;
    use App\Http\Controllers\ChatController;
    $matchId = request()->query('match_id');
    if ($matchId) {
        $match = Calendario::where('match_id', $matchId)->first();
        MatchStaticsController::storeMatchStatistics($matchId);
        MatchLineupsController::storeLineups($matchId);
        // MatchCommentaryController::storeCommentaries($matchId);
        MatchSummaryController::storeMatchSummary($matchId);

        $lineups = MatchLineups::where('match_id', $matchId)->get();
        $statics = MatchStatics::where('match_id', $matchId)->get();
        // Use custom SQL logic to sort the comment_time field
        $commentaries = MatchCommentary::where('match_id', $matchId)
            ->where(function ($query) {
                $query->whereNotNull('comment_time')->orWhereNotNull('comment_class')->orWhereNotNull('comment_text');
            })
            ->orderByRaw(
                "
        CAST(SUBSTRING_INDEX(comment_time, \"'\", 1) AS UNSIGNED) +
        IF(LOCATE('+', comment_time) > 0,
            CAST(SUBSTRING_INDEX(SUBSTRING_INDEX(comment_time, \"'\", 1), '+', -1) AS UNSIGNED),
            0
        )
    ",
            )
            ->get();

        $summaries = MatchSummary::where('match_id', $matchId)->get();

        $fiorentinaLineups = $lineups
            ->filter(function ($lineup) {
                return in_array($lineup->formation_name, [
                    'Fiorentina Subs',
                    'Fiorentina Coach',
                    'Fiorentina Initial Lineup',
                ]);
            })
            ->groupBy('formation_name');

        $anotherTeamLineups = $lineups
            ->filter(function ($lineup) {
                return in_array($lineup->formation_name, ['Another Subs', 'Another Coach', 'Another Initial Lineup']);
            })
            ->groupBy('formation_name');

        //create the chat if it doesn't exist
    }
@endphp


@if (isset($match))
    @php
        $homeTeam = json_decode($match->home_team, true);
        $awayTeam = json_decode($match->away_team, true);
        $score = json_decode($match->score, true);
        $odds = json_decode($match->odds, true);

        $isHomeFiorentina =
            $homeTeam['name'] == 'Fiorentina' ||
            $homeTeam['name'] == 'Fiorentina (Ita)' ||
            $homeTeam['name'] == 'Fiorentina (Ita) *';
        $isAwayFiorentina =
            $awayTeam['name'] == 'Fiorentina' ||
            $awayTeam['name'] == 'Fiorentina (Ita)' ||
            $awayTeam['name'] == 'Fiorentina (Ita) *';

    @endphp
    <div class="match-details mt-5">
        <div class="team-logos">
            <div class="team home-team">
                <img src="{{ $homeTeam['logo'] }}" alt="{{ $homeTeam['name'] }}">
                <span>{{ $homeTeam['name'] }}</span>
            </div>
            <div id="score-{{ $match->id }}" class="match-score">
                <h6 id="match-date-{{ $match->id }}">
                    {{ date('d.m.Y H:i', strtotime($match->match_date)) }}
                </h6>

                <div id="live-score-{{ $match->id }}">
                    {{ $score['home'] }} - {{ $score['away'] }}
                </div>
            </div>
            <div class="team away-team">
                <img src="{{ $awayTeam['logo'] }}" alt="{{ $awayTeam['name'] }}">
                <span>{{ $awayTeam['name'] }}</span>
            </div>
        </div>
    </div>


    <!-- Tab Navigation -->
    <ul class="nav nav-tabs mt-5" id="myTab" role="tablist">
        <li class="nav-item" role="presentation">
            <a class="nav-link @if ($match->status != 'LIVE') active @endif" id="formazioni-tab" data-toggle="tab"
                href="#formazioni" role="tab" aria-controls="formazioni" aria-selected="false">FORMAZIONI</a>
        </li>

        <li class="nav-item" role="presentation">
            <a class="nav-link" id="riassunto-tab" data-toggle="tab" href="#riassunto" role="tab"
                aria-controls="riassunto" aria-selected="true">RIASSUNTO</a>
        </li>
        <li class="nav-item" role="presentation">
            <a class="nav-link" id="statistiche-tab" data-toggle="tab" href="#statistiche" role="tab"
                aria-controls="statistiche" aria-selected="false">STATISTICHE</a>
        </li>

        <li class="nav-item" role="presentation">
            <a class="nav-link @if ($match->status == 'LIVE') active @endif " id="commento-tab" data-toggle="tab"
                href="#commento" role="tab" aria-controls="commento" aria-selected="false">DIRETTA</a>
        </li>
        @if ($match->status == 'FINISHED')
            <li class="nav-item" role="presentation">
                <a class="nav-link" id="poll-tab" data-toggle="tab" href="#poll" role="tab" aria-controls="poll"
                    aria-selected="false">POLLS</a>
            </li>
        @endif

    </ul>

    <!-- Tab Content -->
    <div class="tab-content" id="myTabContent">
        <div class="tab-pane fade @if ($match->status != 'LIVE') show active @endif text-dark" id="formazioni"
            role="tabpanel" aria-labelledby="formazioni-tab">
            <ul class="nav nav-tabs mt-5" id="teamtab" role="tablist">
                <li class="nav-item" role="presentation" style="list-style: none;">
                    <a class="nav-link @if ($isHomeFiorentina) active @endif" id="Home-tab" data-toggle="tab"
                        href="#home" role="tab" aria-controls="home" aria-selected="true">Home</a>
                </li>
                <li class="nav-item" style="list-style: none;" role="presentation">
                    <a class="nav-link @if ($isAwayFiorentina) active @endif" id="Away-tab" data-toggle="tab"
                        href="#away" role="tab" aria-controls="away" aria-selected="false">Away</a>
                </li>
            </ul>

            {{-- ads/includes/formazioni-tabs.blade.php --}}

            {{-- first paint – identical to what you have now --}}

            @include('ads.includes.formazioni-tabs', [
                'isHomeFiorentina' => $isHomeFiorentina,
                'isAwayFiorentina' => $isAwayFiorentina,
                'fiorentinaLineups' => $fiorentinaLineups,
                'anotherTeamLineups' => $anotherTeamLineups,
                'match' => $match,
            ])



        </div>
        <div class="tab-pane fade" id="riassunto" role="tabpanel" aria-labelledby="riassunto-tab">
            @include('ads.includes.riassunto', ['summaries' => $summaries])

        </div>
        <div class="tab-pane fade" id="statistiche" role="tabpanel" aria-labelledby="statistiche-tab">
            @include('ads.includes.statistiche', [
                'statics' => $statics,
                'isHomeFiorentina',
                $isHomeFiorentina,
            ])
        </div>

        <div class="tab-pane @if ($match->status == 'LIVE') show active @endif fade" id="commento" role="tabpanel"
            aria-labelledby="commento-tab">

            @include('ads.includes.livecommentary', ['match_id', $matchId]);


        </div>
        @if ($match->status == 'FINISHED')
            <div class="tab-pane fade" id="poll" role="tabpanel" aria-labelledby="poll-tab">
                @include('ads.includes.polls', [
                    'lineup' => $fiorentinaLineups,
                ])

            </div>
        @endif
    </div>
    {{-- Diretta History blade --}}
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const endpoint = "{{ route('match.score', $match) }}";
            const scoreNode = document.getElementById('live-score-{{ $match->id }}');

            // poll every 30 s (adjust as you like)
            setInterval(() => {
                axios.get(endpoint)
                    .then(({
                        data
                    }) => {
                        scoreNode.textContent = `${data.home} - ${data.away}`;
                    })
                    .catch(err => console.error(err));
            }, 10000);
        });

        document.addEventListener('DOMContentLoaded', () => {
            const container = document.getElementById('teamtabContent');
            const endpoint = "{{ route('match.lineup-block', $match) }}";
            const TEN_MIN = 600_000; // 10 min

            setInterval(() => {
                // remember which tab is active so we can keep it open
                const activeTabId = container.querySelector('.tab-pane.show.active')?.id ?? 'home';

                axios.get(endpoint).then(({
                    data
                }) => {
                    container.innerHTML = data;

                    // restore the user’s tab if possible
                    const newActive = container.querySelector('#' + activeTabId);
                    if (newActive) {
                        newActive.classList.add('show', 'active');
                        // Also toggle the nav-link if you use Bootstrap tabs
                        const nav = document.querySelector(`[data-bs-target="#${activeTabId}"]`);
                        nav?.classList.add('active');
                    }
                }).catch(console.error);
            }, 60000000);
        });
    </script>
@else
@endif
