@php
    use Illuminate\Support\Str;
    use App\Http\Controllers\MatchLineupsController;
    use App\Models\PlayerStats;
    $formationInitiali = collect();
    $panchina = collect();
    $Allenatori = collect();

    if ($team === 'fiorentina') {
        $formationInitiali = $groupedLineups['Fiorentina Initial Lineup'] ?? collect();
        $panchina = $groupedLineups['Fiorentina Subs'] ?? collect();
        $Allenatori = $groupedLineups['Fiorentina Coach'] ?? collect();
    } elseif ($team === 'another') {
        $formationInitiali = $groupedLineups['Another Initial Lineup'] ?? collect();
        $panchina = $groupedLineups['Another Subs'] ?? collect();
        $Allenatori = $groupedLineups['Another Coach'] ?? collect();
    }

    // if there’s no initial lineup, bail early
    if ($formationInitiali->isEmpty()) {
        echo '<p>Nessuna formazione disponibile.</p>';
        return;
    }

    // Sort & build rows
    $formationInitiali = $formationInitiali->sortBy('player_position');
    $formationDisposition = $formationInitiali->first()->formation_disposition ?? '';
    $cleanDisposition = preg_replace('/^\d+-/', '', $formationDisposition);

    $formationArray = array_filter(explode('-', $formationDisposition));

    $playerRows = [];
    $currentIndex = 0;
    foreach ($formationArray as $num) {
        $playerRows[] = $formationInitiali->slice($currentIndex, $num);
        $currentIndex += $num;
    }
    $playerRows = array_reverse($playerRows);

@endphp

@inject('playerRepo', 'App\\Models\\Player')

@php
    if (!function_exists('lineupImgSrc')) {
        function lineupImgSrc(object $flashPlayer, string $team, $playerRepo): ?string
        {
            // 1️⃣ Fiorentina: try the local DB first
            if ($team === 'fiorentina') {
                $dbPlayer = $playerRepo
                    ->where('name', 'like', $flashPlayer->short_name) // or player_id, flashscore_id, …
                    ->first();

                if ($dbPlayer && $dbPlayer->image) {
                    // Same “https:// …” vs Wasabi logic you already use elsewhere
                    return Str::startsWith($dbPlayer->image, 'https://')
                        ? $dbPlayer->image
                        : $dbPlayer->wasabiImage($dbPlayer->name);
                }
            }

            // 2️⃣ Anything not found above → keep the image that came with the API
            if ($flashPlayer->player_image) {
                return $flashPlayer->player_image;
            }

            // 3️⃣ Nothing at all
            return null;
        }
    }
@endphp


<div class="row">
    <div class="football-pitch">
        <div class="pitch-lines"></div>
        <div class="halfway-line"></div>

        <!-- Penalty areas -->
        <div class="penalty-area-top"></div>
        <div class="penalty-area-bottom"></div>

        <!-- Small boxes inside the penalty areas -->
        <div class="small-box-top"></div>
        <div class="small-box-bottom"></div>

        <div class="container">
            <!-- Display Formation Header -->
            <div class="row">
                <div class="col-12">
                    <h2 class="pl-5 text-dark text-bold">Formazioni Iniziali</h2>
                    <p class="pl-5 text-dark text-bold">Formation: {{ $cleanDisposition }}</p>
                </div>
            </div>

            <!-- Loop through each row (group of players) in the formation and display the players -->
            @foreach ($playerRows as $row)
                <div class="row justify-content-around mb-4" style="flex-direction: row-reverse;">
                    @foreach ($row as $player)
                        <div class="col text-center">
                            <div class="player-container">
                                <div class="player-lineup">
                                    @php $imgSrc = lineupImgSrc($player, $team, $playerRepo)@endphp
                                    @if ($imgSrc)
                                        <img src="{{ $imgSrc }}" alt="{{ $player->player_full_name }}">
                                    @endif
                                    <div class="rating"
                                        @if ($player->player_rating >= 7.0) style='background-color: #1dc231;'
                                    @elseif ($player->player_rating <= 6.1)
                                        style='background-color: #c21d1d;' @endif>

                                        {{ $player->player_rating }}
                                    </div>
                                    <p class="player-name">{{ $player->short_name }}</p>
                                </div>
                            </div>

                        </div>
                    @endforeach
                </div>
            @endforeach
        </div>


    </div>
    <div class="col-md-4">
        <h5 class="mt-5 pl-5 text-dark text-bold">Panchina</h5>
        <table class="table table-responsive">

            <tbody>
                @foreach ($panchina as $panchinaPlayer)
                    <tr>
                        <td style="text-align:left">

                            @if ($panchinaPlayer->player_image)
                                @php $imgSrc = lineupImgSrc($panchinaPlayer, $team, $playerRepo) @endphp
                                @if ($imgSrc)
                                    <img src="{{ $imgSrc }}" class="mr-20"
                                        alt="{{ $panchinaPlayer->player_full_name }}">
                                @endif
                            @else
                                <svg style="width:50px" class="_icon_1483j_4 _image_1b9ls_29"
                                    data-testid="wcl-icon-placeholder-man" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd"
                                        d="m18.4 16.29-5.06-1.52-.43-1.55a7.78 7.78 0 0 0 2.25-5.41c.02-2.69-.78-4.46-1-4.9l.25-1.96H9.95c-1.57 0-2.8.45-3.67 1.34-1.1 1.14-1.6 2.94-1.54 5.52a8.02 8.02 0 0 0 2.34 5.44l-.42 1.52-5.06 1.52-1.6 1.6V20h20v-2.11l-1.6-1.6Z"
                                        fill="#fff"></path>
                                    <path fill-rule="evenodd"
                                        d="M11.12 17.8h-2.3l-2.3-2.28.7-.2.43-1.57c.75.57 1.56.9 2.3.9.78 0 1.62-.34 2.39-.93l.44 1.6.64.19-2.3 2.3Zm-5.7-10c0-.45 0-.86.03-1.25L9 5.66l2.84 1.4v-1.3l2.6.87c.02.37.04.76.04 1.17-.02 3.58-2.72 6.17-4.53 6.17-1.96 0-4.44-2.85-4.52-6.17Zm1.35-5.03C7.52 2 8.56 1.64 9.95 1.64h3.68l-.17 1.4.05.1c0 .01.56 1.03.83 2.74L11.15 4.8v1.16L9.08 4.94l-3.57.89a5.18 5.18 0 0 1 1.26-3.06ZM18.4 16.29l-5.06-1.52-.43-1.55a7.78 7.78 0 0 0 2.25-5.41c.02-2.69-.78-4.46-1-4.9l.25-1.96H9.95c-1.57 0-2.8.45-3.67 1.34-1.1 1.14-1.6 2.94-1.54 5.52a8.02 8.02 0 0 0 2.34 5.44l-.42 1.52-5.06 1.52-1.6 1.6V20h.68v-1.83l1.28-1.28 3.82-1.15 2.75 2.75h2.87l2.77-2.76 3.87 1.16 1.28 1.28V20H20v-2.11l-1.6-1.6Z"
                                        fill="#000"></path>
                                </svg>
                            @endif


                            {{ $panchinaPlayer->short_name }}

                            @if ($panchinaPlayer->player_rating)
                                <span class="rating-table"
                                    @if ($panchinaPlayer->player_rating >= 7.0) style="background-color: #1dc231;"
                                 @elseif ($panchinaPlayer->player_rating <= 6.1) style="background-color: #c21d1d;" @endif>
                                    {{ $panchinaPlayer->player_rating }}
                                </span>
                            @else
                                <span class="rating-table" style="background-color: #ffffff00; color:black">
                                    -
                                </span>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>


    <div class="col-md-4">
        <h5 class="mt-5 pl-5 text-dark text-bold">Allenatore</h5>
        <table class="table table-responsive">

            <tbody>
                @foreach ($Allenatori as $Allenatore)
                    <tr>
                        <td style="text-align: left">

                            @if ($Allenatore->Allenatore)
                                <img src="{{ $panchinaPlayer->player_image }}"
                                    alt="{{ $panchinaPlayer->player_full_name }}" class="mr-20">
                            @else
                                <svg style="width:50px" class="_icon_1483j_4 _image_1b9ls_29"
                                    data-testid="wcl-icon-placeholder-man" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd"
                                        d="m18.4 16.29-5.06-1.52-.43-1.55a7.78 7.78 0 0 0 2.25-5.41c.02-2.69-.78-4.46-1-4.9l.25-1.96H9.95c-1.57 0-2.8.45-3.67 1.34-1.1 1.14-1.6 2.94-1.54 5.52a8.02 8.02 0 0 0 2.34 5.44l-.42 1.52-5.06 1.52-1.6 1.6V20h20v-2.11l-1.6-1.6Z"
                                        fill="#fff"></path>
                                    <path fill-rule="evenodd"
                                        d="M11.12 17.8h-2.3l-2.3-2.28.7-.2.43-1.57c.75.57 1.56.9 2.3.9.78 0 1.62-.34 2.39-.93l.44 1.6.64.19-2.3 2.3Zm-5.7-10c0-.45 0-.86.03-1.25L9 5.66l2.84 1.4v-1.3l2.6.87c.02.37.04.76.04 1.17-.02 3.58-2.72 6.17-4.53 6.17-1.96 0-4.44-2.85-4.52-6.17Zm1.35-5.03C7.52 2 8.56 1.64 9.95 1.64h3.68l-.17 1.4.05.1c0 .01.56 1.03.83 2.74L11.15 4.8v1.16L9.08 4.94l-3.57.89a5.18 5.18 0 0 1 1.26-3.06ZM18.4 16.29l-5.06-1.52-.43-1.55a7.78 7.78 0 0 0 2.25-5.41c.02-2.69-.78-4.46-1-4.9l.25-1.96H9.95c-1.57 0-2.8.45-3.67 1.34-1.1 1.14-1.6 2.94-1.54 5.52a8.02 8.02 0 0 0 2.34 5.44l-.42 1.52-5.06 1.52-1.6 1.6V20h.68v-1.83l1.28-1.28 3.82-1.15 2.75 2.75h2.87l2.77-2.76 3.87 1.16 1.28 1.28V20H20v-2.11l-1.6-1.6Z"
                                        fill="#000"></path>
                                </svg>
                            @endif



                            {{ $Allenatore->short_name }}

                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
