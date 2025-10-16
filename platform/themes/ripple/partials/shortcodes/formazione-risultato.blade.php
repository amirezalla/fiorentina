@php
    use App\Support\FormationStats;
    use Illuminate\Support\Str;
    use Illuminate\Support\Arr;

    // FIXED: use Str::of(...)->replace(...) or plain str_replace(...)
    $fmtDate = fn($d) => Str::of(
        \Carbon\Carbon::parse($d)->locale('it')->timezone('Europe/Rome')->isoFormat('dddd D MMMM H:mm'),
    )
        ->replace(' ore ', ' ')
        ->toString();
    $home = $match ? FormationStats::teamInfo($match->home_team) : ['name' => '—', 'logo' => null];
    $away = $match ? FormationStats::teamInfo($match->away_team) : ['name' => '—', 'logo' => null];

    // image resolver (handles Wasabi/absolute)
    $imgUrl = function ($p) {
        if (!$p) {
            return null;
        }
        $img = $p->image ?? '';
        if (!$img) {
            return null;
        }
        if (Str::startsWith($img, ['http://', 'https://'])) {
            return $img;
        }
        try {
            return \Storage::disk('wasabi')->temporaryUrl($img, now()->addMinutes(20));
        } catch (\Throwable $e) {
            return null;
        }
    };
@endphp

<div class="mb-3" style="background:#8424e3;color:#fff;border-radius:.5rem;padding:.8rem 1rem;">
    <div class="d-flex align-items-center justify-content-between">
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
        </div>
        @if ($match)
            <div>{{ $fmtDate($match->match_date) }}</div>
        @endif
    </div>
</div>

@if (($data['totalVotes'] ?? 0) === 0)
    <div class="card">
        <div class="card-body text-muted">Nessun voto registrato per questa partita.</div>
    </div>
@else
    {{-- Formazione più votata --}}
    <div class="card mb-3">
        <div class="card-header">
            <strong>Formazione più votata</strong>
            <span class="ml-2 text-muted">Voti: {{ $data['totalVotes'] }}</span>
            <span class="badge badge-pill badge-primary ml-2">{{ $data['topFormation'] }}</span>
        </div>
        <div class="card-body">
            @php
                $slots = collect($data['slots']);
                $rows = [
                    'GK' => $slots->filter(fn($v, $k) => $k === 'GK'),
                    'DF' => $slots->filter(fn($v, $k) => Str::startsWith($k, 'DF')),
                    'MF' => $slots->filter(fn($v, $k) => Str::startsWith($k, 'MF')),
                    'FW' => $slots->filter(fn($v, $k) => Str::startsWith($k, 'FW')),
                ];
            @endphp

            @foreach (['GK', 'DF', 'MF', 'FW'] as $line)
                @if ($rows[$line]->count())
                    <div class="d-flex justify-content-center flex-wrap mb-3">
                        @foreach ($rows[$line] as $slot => $info)
                            @php
                                $p = $info['player'];
                                $photo = $imgUrl($p);
                            @endphp
                            <div class="mx-2 my-1 p-2 text-center position-relative"
                                style="width:120px;border:1px solid #eee;border-radius:10px;">
                                {{-- percentage badge top-right --}}
                                <span class="badge position-absolute"
                                    style="top:6px; right:6px; background:#8424e3;color:#fff;">
                                    {{ $info['perc'] }}%
                                </span>

                                @if ($photo)
                                    <img src="{{ $photo }}" alt="{{ $p->name }}"
                                        style="width:68px;height:68px;object-fit:cover;border-radius:50%;
                                                border:2px solid #8424e3;">
                                @else
                                    <div
                                        style="width:68px;height:68px;border-radius:50%;
                                                background:#f0e8ff;margin:auto;border:2px solid #8424e3;
                                                display:flex;align-items:center;justify-content:center;
                                                font-weight:800;color:#8424e3;">
                                        {{ Str::upper(Str::substr(last(explode(' ', $p->name ?? '')), 0, 1)) }}
                                    </div>
                                @endif

                                <div class="mt-2 font-weight-bold" style="font-size:.9rem">{{ $p->name ?? '—' }}</div>
                                <div class="mt-1">
                                    <span class="badge"
                                        style="background:#8424e3;color:#fff;">#{{ $p->jersey_number ?? '—' }}</span>
                                </div>
                                <div class="text-muted" style="font-size:.75rem">{{ $slot }}</div>
                            </div>
                        @endforeach
                    </div>
                @endif
            @endforeach
        </div>
    </div>

    {{-- Statistica modulo --}}
    <div class="card mb-4">
        <div class="card-header"><strong>Statistica Modulo</strong></div>
        <div class="card-body">
            @foreach ($data['formationBreakdown'] as $form => $count)
                @php $perc = (int) round($count * 100 / $data['totalVotes']); @endphp
                <div class="mb-2">
                    <div class="d-flex justify-content-between">
                        <span class="font-weight-bold">{{ $form }}</span>
                        <span class="text-muted">{{ $perc }}%</span>
                    </div>
                    <div style="background:#f3eefc;border-radius:6px;height:8px;overflow:hidden;">
                        <div style="width:{{ $perc }}%;background:#8424e3;height:100%;"></div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
@endif
