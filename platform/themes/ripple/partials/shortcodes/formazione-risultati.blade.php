@php
    use Illuminate\Support\Arr;
    use Carbon\Carbon;

    $fmtDate = function ($ts) {
        return \Carbon\Carbon::parse($ts)->locale('it')->timezone('Europe/Rome')->isoFormat('ddd D MMM HH:mm'); // ← niente “ore”
    };

    $teamLogo = function ($json) {
        $a = json_decode($json ?? '{}', true);
        return [
            'name' => Arr::get($a, 'name', '—'),
            'logo' => Arr::get($a, 'logo', null),
        ];
    };

    $imgUrl = function ($player) {
        if (!$player) {
            return null;
        }
        $img = $player->image;
        if ($img && (str_starts_with($img, 'http://') || str_starts_with($img, 'https://'))) {
            return $img;
        }
        try {
            return Storage::disk('wasabi')->url($img);
        } catch (\Throwable $e) {
            return null;
        }
    };
@endphp

<div class="formazione-results-widget">

    {{-- NEXT MATCH (highlight) --}}
    @if ($nextMatch)
        @php
            $home = $teamLogo($nextMatch->home_team);
            $away = $teamLogo($nextMatch->away_team);
        @endphp

        <div class="lv-next-banner mb-3">
            <div class="lv-next-left">
                @if ($home['logo'])
                    <img src="{{ $home['logo'] }}" class="lv-team-logo mr-2" alt="{{ $home['name'] }}">
                @endif
                <strong class="mr-2">{{ $home['name'] }}</strong>
                <span class="mx-1">vs</span>
                @if ($away['logo'])
                    <img src="{{ $away['logo'] }}" class="lv-team-logo mx-2" alt="{{ $away['name'] }}">
                @endif
                <strong>{{ $away['name'] }}</strong>
            </div>

            <div class="lv-next-right">
                <span class="lv-next-date mr-5">{{ $fmtDate($nextMatch->match_date) }}</span>
                <a href="/prossima-partita-formazione-dei-tifosi" class="lv-btn-white-sm">
                    Vota formazione
                </a>
            </div>
        </div>
    @endif


    {{-- ULTIMO RISULTATO VOTAZIONE (compact panel) --}}
    <div class="card mb-4" style="border:0;">
        <div class="card-header" style="border:0; background:#fff;">
            <strong>Ultimo risultato votazione</strong>
        </div>

        <div class="card-body p-0">
            @if ($lastFinished)
                @php
                    $home = $teamLogo($lastFinished->home_team);
                    $away = $teamLogo($lastFinished->away_team);
                    $when = $fmtDate($lastFinished->match_date);
                @endphp

                <div class="d-flex align-items-center justify-content-between"
                    style="border:1px solid #e4d8fb;background:#f3eefc;color:#151515;border-radius:.5rem;padding:.8rem 1rem;">
                    <div class="d-flex align-items-center">
                        @if ($home['logo'])
                            <img src="{{ $home['logo'] }}" style="height:28px" class="mr-2" alt="">
                        @endif
                        <strong class="mr-2">{{ $home['name'] }}</strong>
                        <span class="mx-1">vs</span>
                        @if ($away['logo'])
                            <img src="{{ $away['logo'] }}" style="height:28px" class="mx-2" alt="">
                        @endif
                        <strong>{{ $away['name'] }}</strong>
                    </div>

                    <div class="d-flex align-items-center">
                        <span class="mr-3" style="opacity:.9">{{ $when }}</span>
                        <a href="{{ url('risultati?match_id=' . ($lastFinished->match_id ?? '4I6uQ5ll')) }}"
                            class="lv-btn-ghost" style="background:#fff;color:#4b2d7f;border:0;">
                            Vedi risultato
                        </a>
                    </div>
                </div>
            @else
                <div class="p-3 text-muted">Nessuna partita terminata.</div>
            @endif
        </div>
    </div>

    {{-- ===================== ARCHIVIO ===================== --}}
    <div class="archivio-formazioni mb-4">
        <h3 class="mb-3" style="font-weight:700;">Formazioni in archivio</h3>

        <ul class="archivio-list list-unstyled m-0 p-0">
            @forelse ($archive as $m)
                @php
                    $url = url('/formazione/risultati?match_id=' . $m['match_id']);
                    $date = \Carbon\Carbon::parse($m['date'])->format('d/m/Y');
                @endphp

                <li class="archivio-row">
                    <a class="archivio-title" href="{{ $url }}">
                        <span class="chevron">▸</span>
                        {{ $m['home'] }} vs {{ $m['away'] }}
                    </a>

                    <a class="archivio-date" href="{{ $url }}">
                        {{ $date }}
                    </a>
                </li>
            @empty
                <li class="archivio-row empty">
                    <span class="archivio-title">
                        Nessuna partita in archivio.
                    </span>
                </li>
            @endforelse
        </ul>
    </div>

    <style>
        /* container */
        .archivio-formazioni h3 {
            margin-bottom: .75rem;
        }

        /* rows */
        .archivio-list .archivio-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: .6rem .75rem;
            background: #f4f4f6;
            /* light grey */
        }

        .archivio-list .archivio-row:nth-child(even) {
            background: #ececf0;
            /* alternate stripe */
        }

        /* links + colors */
        .archivio-title,
        .archivio-date {
            color: #8424e3;
            text-decoration: none;
            font-weight: 600;
        }

        .archivio-title:hover,
        .archivio-date:hover {
            text-decoration: underline;
        }

        /* left chevron */
        .archivio-title .chevron {
            display: inline-block;
            margin-right: .45rem;
            color: #8424e3;
        }

        /* mobile wrap */
        @media (max-width:576px) {
            .archivio-list .archivio-row {
                flex-wrap: wrap;
            }

            .archivio-date {
                margin-top: .25rem;
            }
        }

        .lv-team-logo {
            height: 28px;
            width: auto;
        }

        /* Purple banner */
        .lv-next-banner {
            display: flex;
            align-items: center;
            justify-content: space-between;
            background: #8424e3;
            color: #fff;
            border: 0;
            border-radius: 8px;
            /* adjust if you want 0 */
            padding: .75rem .9rem;
        }

        /* Left (teams) */
        .lv-next-left {
            display: flex;
            align-items: center;
            flex-wrap: wrap;
        }

        /* Right (date + CTA) */
        .lv-next-right {
            display: flex;
            align-items: center;
        }

        .lv-next-date {
            opacity: .95;
            font-weight: 600;
            white-space: nowrap;
        }

        /* White small CTA, sharp corners */
        .lv-btn-white-sm {
            background: #fff;
            color: #4b2d7f;
            border: 0;
            border-radius: 0;
            font-weight: 700;
            padding: .45rem .75rem;
            text-decoration: none;
        }

        .lv-btn-white-sm:hover {
            background: #f7f7ff;
            color: #3a1f68;
        }

        /* Mobile: stack and push date+button to next line */
        @media (max-width: 576px) {
            .lv-next-banner {
                flex-direction: column;
                align-items: flex-start;
                gap: .4rem;
            }

            .lv-next-right {
                padding-left: 0;
            }

            .lv-next-date {
                white-space: normal;
                margin-right: .5rem !important;
            }
        }
    </style>

</div>
