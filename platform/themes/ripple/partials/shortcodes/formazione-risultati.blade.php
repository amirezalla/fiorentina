@php
    use Illuminate\Support\Arr;
    use Carbon\Carbon;

    $fmtDate = function ($dt) {
        return Carbon::parse($dt)->locale('it')->timezone('Europe/Rome')->isoFormat('ddd D MMM [ore] H:mm');
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
        <div class="card mb-3" style="border:0; background:#8424e3; color:#fff;">
            <div class="card-body d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center">
                    @if ($home['logo'])
                        <img src="{{ $home['logo'] }}" style="height:28px" class="mr-2">
                    @endif
                    <strong class="mr-2">{{ $home['name'] }}</strong>
                    <span class="mx-1">vs</span>
                    @if ($away['logo'])
                        <img src="{{ $away['logo'] }}" style="height:28px" class="mx-2">
                    @endif
                    <strong>{{ $away['name'] }}</strong>
                    <span class="ml-3" style="opacity:.9">{{ $fmtDate($nextMatch->match_date) }}</span>
                </div>
                <a href="{{ route('formazione.index') }}" class="btn btn-light btn-sm font-weight-bold">
                    Vota formazione
                </a>
            </div>
        </div>
    @endif

    {{-- LAST FINISHED RESULT --}}
    <div class="card mb-4">
        <div class="card-header d-flex align-items-center justify-content-between">
            <div>
                <strong>Ultimo risultato votazione</strong>
                @if ($lastFinished)
                    @php
                        $home = $teamLogo($lastFinished->home_team);
                        $away = $teamLogo($lastFinished->away_team);
                    @endphp
                    <span class="ml-2 text-muted">
                        {{ $home['name'] }} – {{ $away['name'] }} • {{ $fmtDate($lastFinished->match_date) }}
                    </span>
                @else
                    <span class="ml-2 text-muted">Nessuna partita terminata</span>
                @endif
            </div>
            @if (($aggregate['totalVotes'] ?? 0) > 0 && $aggregate['topFormation'])
                <span class="badge badge-pill badge-primary">Formazione più votata:
                    {{ $aggregate['topFormation'] }}</span>
            @endif
        </div>

        <div class="card-body">
            @if (!$lastFinished)
                <div class="text-muted">Nessun dato disponibile.</div>
            @elseif (($aggregate['totalVotes'] ?? 0) === 0)
                <div class="text-muted">Nessun voto registrato per questa partita.</div>
            @else
                <div class="row">
                    @php
                        // Render in lines: GK, DF*, MF*, FW* based on slot keys
                        $slots = collect($aggregate['slots']);
                        $rows = [
                            'GK' => $slots->filter(fn($v, $k) => $k === 'GK'),
                            'DF' => $slots->filter(fn($v, $k) => str_starts_with($k, 'DF')),
                            'MF' => $slots->filter(fn($v, $k) => str_starts_with($k, 'MF')),
                            'FW' => $slots->filter(fn($v, $k) => str_starts_with($k, 'FW')),
                        ];
                    @endphp

                    @foreach (['GK', 'DF', 'MF', 'FW'] as $line)
                        @if ($rows[$line]->count())
                            <div class="col-12 mb-3">
                                <div class="d-flex justify-content-center flex-wrap">
                                    @foreach ($rows[$line] as $slot => $info)
                                        @php
                                            $p = $info['player'];
                                            $photo = $imgUrl($p);
                                        @endphp
                                        <div class="mx-2 my-1 p-2 text-center"
                                            style="width:110px;border:1px solid #eee;border-radius:10px;">
                                            @if ($photo)
                                                <img src="{{ $photo }}" alt="{{ $p->name }}"
                                                    style="width:64px;height:64px;object-fit:cover;border-radius:50%;border:2px solid #8424e3;">
                                            @else
                                                <div
                                                    style="width:64px;height:64px;border-radius:50%;background:#f0f0f0;margin:auto">
                                                </div>
                                            @endif
                                            <div class="mt-2 font-weight-bold" style="font-size:.9rem">
                                                {{ $p->name ?? '—' }}</div>
                                            <div class="mt-1">
                                                <span class="badge badge-pill" style="background:#8424e3;color:#fff;">
                                                    #{{ $p->jersey_number ?? '—' }}
                                                </span>
                                                <span class="badge badge-light ml-1">{{ $info['perc'] }}%</span>
                                            </div>
                                            <div class="text-muted" style="font-size:.75rem">{{ $slot }}</div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    @endforeach
                </div>

                <div class="small text-muted">
                    Voti raccolti: <strong>{{ $aggregate['totalVotes'] }}</strong>
                </div>
            @endif
        </div>
    </div>

    {{-- ARCHIVE --}}
    <div class="card">
        <div class="card-header">
            <strong>Archivio votazioni (recenti)</strong>
        </div>
        <div class="list-group list-group-flush">
            @forelse ($archive as $m)
                <div class="list-group-item d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-muted mr-2">{{ \Carbon\Carbon::parse($m['date'])->format('d/m/Y') }}</span>
                        <strong>{{ $m['home'] }}</strong>
                        <span class="mx-1">–</span>
                        <strong>{{ $m['away'] }}</strong>
                        <span class="ml-2 text-muted">Voti: {{ $m['votes'] }}</span>
                    </div>
                    <a class="btn btn-outline-primary btn-sm"
                        href="{{ url('/formazione/risultati?match_id=' . $m['match_id']) }}">
                        Vedi risultato
                    </a>
                </div>
            @empty
                <div class="list-group-item text-muted">Nessuna partita in archivio.</div>
            @endforelse
        </div>
    </div>

</div>
