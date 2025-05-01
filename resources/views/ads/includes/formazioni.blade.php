@php
    use Illuminate\Support\Str;

    /* ---------------------------------------------------------
       1.  Pick the three blocks that correspond to the side
    ----------------------------------------------------------*/

    $teamKeys = [
        'fiorentina' => [
            'initial' => 'Fiorentina Initial Lineup',
            'subs' => 'Fiorentina Subs',
            'coach' => 'Fiorentina Coach',
        ],
        'another' => [
            'initial' => 'Another Initial Lineup',
            'subs' => 'Another Subs',
            'coach' => 'Another Coach',
        ],
    ];

    $keys = $teamKeys[$team] ?? $teamKeys['another']; // fallback safety

    // $groupedLineups may be an array → wrap in Collection once
    $lineups = collect($groupedLineups);

    $initial = $lineups->get($keys['initial'], collect());
    $bench = $lineups->get($keys['subs'], collect());
    $coaches = $lineups->get($keys['coach'], collect());
    dd($team, $initial, $bench);

    /* ---------------------------------------------------------
       2.  Bail out early if no starting XI
    ----------------------------------------------------------*/
    if ($initial->isEmpty()) {
        echo '<p>Nessuna formazione disponibile.</p>';
        return;
    }

    /* ---------------------------------------------------------
       3.  Order players into rows matching the formation
    ----------------------------------------------------------*/
    $initial = $initial->sortBy('player_position');
    $disposition = $initial->first()->formation_disposition ?? '0-0-0';
    $cleanDisp = preg_replace('/^\d+-/', '', $disposition);

    $layers = array_filter(explode('-', $disposition));
    $rows = [];
    $i = 0;
    foreach ($layers as $n) {
        $rows[] = $initial->slice($i, $n);
        $i += $n;
    }
    $rows = array_reverse($rows); // goalkeeper lowest, forwards top

    /* ---------------------------------------------------------
       4.  Helper to pick the best image
    ----------------------------------------------------------*/
    if (!function_exists('lineupImgSrc')) {
        function lineupImgSrc(object $player, string $side, $playerRepo): ?string
        {
            // 1️⃣ Local DB (Fiorentina only, as requested)
            if ($side === 'fiorentina') {
                $local = $playerRepo->where('name', 'like', $player->short_name)->first();
                if ($local?->image) {
                    return Str::startsWith($local->image, 'https://')
                        ? $local->image
                        : $local->wasabiImage($local->name);
                }
            }

            // 2️⃣ Fallback to API-supplied image
            return $player->player_image ?: null;
        }
    }
@endphp

@inject('playerRepo', 'App\\Models\\Player')

<div class="row">
    <!-- ==============================================  PITCH  -->
    <div class="football-pitch">
        <div class="pitch-lines"></div>
        <div class="halfway-line"></div>
        <div class="penalty-area-top"></div>
        <div class="penalty-area-bottom"></div>
        <div class="small-box-top"></div>
        <div class="small-box-bottom"></div>

        <div class="container">
            <div class="row">
                <div class="col-12">
                    <h2 class="pl-5 text-dark text-bold">Formazioni Iniziali</h2>
                    <p class="pl-5 text-dark text-bold">Formation: {{ $cleanDisp }}</p>
                </div>
            </div>

            @foreach ($rows as $layer)
                <div class="row justify-content-around mb-4" style="flex-direction: row-reverse;">
                    @foreach ($layer as $pl)
                        <div class="col text-center">
                            <div class="player-container">
                                <div class="player-lineup">
                                    @php $src = lineupImgSrc($pl, $team, $playerRepo); @endphp
                                    @if ($src)
                                        <img src="{{ $src }}" alt="{{ $pl->player_full_name }}">
                                    @endif

                                    <div class="rating"
                                        @if ($pl->player_rating >= 7) style="background:#1dc231"
                                         @elseif($pl->player_rating && $pl->player_rating <= 6.1) style="background:#c21d1d" @endif>
                                        {{ $pl->player_rating ?: '-' }}
                                    </div>

                                    <p class="player-name">{{ $pl->short_name }}</p>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endforeach
        </div>
    </div>

    <!-- ==============================================  BENCH  -->
    <div class="col-md-4">
        <h5 class="mt-5 pl-5 text-dark text-bold">Panchina</h5>
        <table class="table table-responsive">
            <tbody>
                @foreach ($bench as $sub)
                    <tr>
                        <td style="text-align:left">
                            @php $src = lineupImgSrc($sub, $team, $playerRepo); @endphp
                            @if ($src)
                                <img src="{{ $src }}" width="50" class="mr-20"
                                    alt="{{ $sub->player_full_name }}">
                            @else
                                {{-- placeholder SVG --}}
                                <svg style="width:50px" viewBox="0 0 20 20" fill="currentColor">
                                    <rect width="20" height="20" fill="#ececec" />
                                </svg>
                            @endif

                            {{ $sub->short_name }}

                            <span class="rating-table"
                                @if ($sub->player_rating >= 7) style="background:#1dc231"
                              @elseif($sub->player_rating && $sub->player_rating <= 6.1) style="background:#c21d1d"
                              @else style="background:#ffffff00;color:#000" @endif>
                                {{ $sub->player_rating ?: '-' }}
                            </span>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- ==============================================  COACH  -->
    <div class="col-md-4">
        <h5 class="mt-5 pl-5 text-dark text-bold">Allenatore</h5>
        <table class="table table-responsive">
            <tbody>
                @foreach ($coaches as $coach)
                    <tr>
                        <td style="text-align:left">
                            @php $src = lineupImgSrc($coach, $team, $playerRepo); @endphp
                            @if ($src)
                                <img src="{{ $src }}" width="50" class="mr-20"
                                    alt="{{ $coach->player_full_name }}">
                            @else
                                {{-- placeholder SVG --}}
                                <svg style="width:50px" viewBox="0 0 20 20" fill="currentColor">
                                    <rect width="20" height="20" fill="#ececec" />
                                </svg>
                            @endif
                            {{ $coach->short_name }}
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
