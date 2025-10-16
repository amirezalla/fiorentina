{{-- ======= Formazione più votata (layout driven by $data['topFormation']) ======= --}}
@php
    use Illuminate\Support\Str;

    // Parse the winning formation and compute how many per role
    $parts = array_map('intval', explode('-', $data['topFormation'] ?? ''));
    // supports both 3-part (D-M-F) and 4-part (D-M1-M2-F)
    if (count($parts) === 3) {
        [$needDF, $needMF, $needFW] = $parts;
    } else {
        [$needDF, $m1, $m2, $needFW] = $parts + [0, 0, 0, 0];
        $needMF = ($m1 ?? 0) + ($m2 ?? 0);
    }
    $need = ['GK' => 1, 'DF' => $needDF ?? 0, 'MF' => $needMF ?? 0, 'FW' => $needFW ?? 0];

    // Build role rows from aggregated slots
    $slots = collect($data['slots'] ?? []);
    $rowsCol = [
        'GK' => $slots->filter(fn($v, $k) => $k === 'GK'),
        'DF' => $slots->filter(fn($v, $k) => \Illuminate\Support\Str::startsWith($k, 'DF')),
        'MF' => $slots->filter(fn($v, $k) => \Illuminate\Support\Str::startsWith($k, 'MF')),
        'FW' => $slots->filter(fn($v, $k) => \Illuminate\Support\Str::startsWith($k, 'FW')),
    ];

    // For each role, pick the top N by percentage (dedup by player id)
    $selected = [];
    foreach (['GK', 'DF', 'MF', 'FW'] as $role) {
        $ranked = $rowsCol[$role]
            ->values()
            ->sortByDesc(fn($x) => $x['perc'] ?? 0)
            ->unique(fn($x) => optional($x['player'])->id)
            ->take($need[$role])
            ->values();
        $selected[$role] = $ranked;
    }

    // Helper to know if a (slot info) is part of the selected set for its role
    $isSelected = function ($role, $info) use ($selected) {
        $pid = optional($info['player'])->id;
        return $selected[$role]->contains(fn($s) => optional($s['player'])->id === $pid);
    };

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

<div class="card mb-3">
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
    <div class="card-header">
        <strong>Formazione più votata</strong>
        <span class="ml-2 text-muted">Voti: {{ $data['totalVotes'] }}</span>
        <span class="badge badge-pill badge-primary ml-2">{{ $data['topFormation'] }}</span>
    </div>

    <div class="card-body">
        {{-- GK (always 1) --}}
        @if ($selected['GK']->count())
            <div class="d-flex justify-content-center mb-3">
                @php
                    $info = $selected['GK'][0];
                    $p = $info['player'];
                    $photo = $imgUrl($p);
                @endphp
                <div class="vx-card is-primary text-center">
                    <span class="vx-pct">{{ $info['perc'] }}%</span>
                    @if ($photo)
                        <img src="{{ $photo }}" alt="{{ $p->name }}">
                    @else
                        @php
                            $parts = preg_split('/\s+/', $p->name ?? '');
                            $initial = \Illuminate\Support\Str::upper(
                                \Illuminate\Support\Str::substr(end($parts) ?: '', 0, 1) ?: '?',
                            );
                        @endphp
                        <div class="vx-initial">{{ $initial }}</div>
                    @endif
                    <div class="vx-name">{{ $p->name ?? '—' }}</div>
                    <div class="vx-badges">
                        <span class="badge badge-primary">#{{ $p->jersey_number ?? '—' }}</span>
                        <span class="badge badge-light">GK</span>
                    </div>
                </div>
            </div>
        @endif

        {{-- DF row --}}
        @if ($need['DF'] > 0)
            <div class="d-flex justify-content-center flex-wrap mb-3">
                @foreach (// show ALL defenders sorted by perc (top N purple, others grey)
    $rowsCol['DF']->values()->sortByDesc(fn($x) => $x['perc'] ?? 0) as $info)
                    @php
                        $p = $info['player'];
                        $photo = $imgUrl($p);
                        $primary = $isSelected('DF', $info);
                    @endphp
                    <div class="vx-card {{ $primary ? 'is-primary' : 'is-muted' }} text-center">
                        <span class="vx-pct">{{ $info['perc'] }}%</span>
                        @if ($photo)
                            <img src="{{ $photo }}" alt="{{ $p->name }}">
                        @else
                            @php
                                $parts = preg_split('/\s+/', $p->name ?? '');
                                $initial = \Illuminate\Support\Str::upper(
                                    \Illuminate\Support\Str::substr(end($parts) ?: '', 0, 1) ?: '?',
                                );
                            @endphp
                            <div class="vx-initial">{{ $initial }}</div>
                        @endif
                        <div class="vx-name">{{ $p->name ?? '—' }}</div>
                        <div class="vx-badges">
                            <span
                                class="badge {{ $primary ? 'badge-primary' : 'badge-secondary' }}">#{{ $p->jersey_number ?? '—' }}</span>
                            <span class="badge badge-light">DF</span>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif

        {{-- MF row --}}
        @if ($need['MF'] > 0)
            <div class="d-flex justify-content-center flex-wrap mb-3">
                @foreach ($rowsCol['MF']->values()->sortByDesc(fn($x) => $x['perc'] ?? 0) as $info)
                    @php
                        $p = $info['player'];
                        $photo = $imgUrl($p);
                        $primary = $isSelected('MF', $info);
                    @endphp
                    <div class="vx-card {{ $primary ? 'is-primary' : 'is-muted' }} text-center">
                        <span class="vx-pct">{{ $info['perc'] }}%</span>
                        @if ($photo)
                            <img src="{{ $photo }}" alt="{{ $p->name }}">
                        @else
                            @php
                                $parts = preg_split('/\s+/', $p->name ?? '');
                                $initial = \Illuminate\Support\Str::upper(
                                    \Illuminate\Support\Str::substr(end($parts) ?: '', 0, 1) ?: '?',
                                );
                            @endphp
                            <div class="vx-initial">{{ $initial }}</div>
                        @endif
                        <div class="vx-name">{{ $p->name ?? '—' }}</div>
                        <div class="vx-badges">
                            <span
                                class="badge {{ $primary ? 'badge-primary' : 'badge-secondary' }}">#{{ $p->jersey_number ?? '—' }}</span>
                            <span class="badge badge-light">MF</span>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif

        {{-- FW row --}}
        @if ($need['FW'] > 0)
            <div class="d-flex justify-content-center flex-wrap">
                @foreach ($rowsCol['FW']->values()->sortByDesc(fn($x) => $x['perc'] ?? 0) as $info)
                    @php
                        $p = $info['player'];
                        $photo = $imgUrl($p);
                        $primary = $isSelected('FW', $info);
                    @endphp
                    <div class="vx-card {{ $primary ? 'is-primary' : 'is-muted' }} text-center">
                        <span class="vx-pct">{{ $info['perc'] }}%</span>
                        @if ($photo)
                            <img src="{{ $photo }}" alt="{{ $p->name }}">
                        @else
                            @php
                                $parts = preg_split('/\s+/', $p->name ?? '');
                                $initial = \Illuminate\Support\Str::upper(
                                    \Illuminate\Support\Str::substr(end($parts) ?: '', 0, 1) ?: '?',
                                );
                            @endphp
                            <div class="vx-initial">{{ $initial }}</div>
                        @endif
                        <div class="vx-name">{{ $p->name ?? '—' }}</div>
                        <div class="vx-badges">
                            <span
                                class="badge {{ $primary ? 'badge-primary' : 'badge-secondary' }}">#{{ $p->jersey_number ?? '—' }}</span>
                            <span class="badge badge-light">FW</span>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</div>

{{-- ======= Statistica Modulo (top in purple, others muted) ======= --}}
@php
    // Sort breakdown by count desc; keep top to compare
    $ordered = collect($data['formationBreakdown'] ?? [])->sortDesc();
    $firstKey = $ordered->keys()->first();
@endphp
<div class="card mb-4">
    <div class="card-header"><strong>Statistica Modulo</strong></div>
    <div class="card-body">
        @foreach ($ordered as $form => $count)
            @php
                $perc = (int) round(($count * 100) / max(1, $data['totalVotes']));
                $isTop = $form === $firstKey;
            @endphp
            <div class="mb-2">
                <div class="d-flex justify-content-between">
                    <span class="{{ $isTop ? 'font-weight-bold' : 'text-muted' }}">{{ $form }}</span>
                    <span class="{{ $isTop ? '' : 'text-muted' }}">{{ $perc }}%</span>
                </div>
                <div style="background:#f3eefc;border-radius:6px;height:8px;overflow:hidden;">
                    <div
                        style="
                        width:{{ $perc }}%;
                        height:100%;
                        background:{{ $isTop ? '#8424e3' : '#cfc6ea' }};
                    ">
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>

<style>
    /* compact player card used on the pitch */
    .vx-card {
        position: relative;
        width: 120px;
        border: 1px solid #eee;
        border-radius: 10px;
        padding: .5rem;
        margin: .25rem .35rem;
        background: #fff;
    }

    .vx-card img {
        width: 68px;
        height: 68px;
        object-fit: cover;
        border-radius: 50%;
        border: 2px solid #8424e3;
    }

    .vx-card .vx-initial {
        width: 68px;
        height: 68px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 800;
        color: #8424e3;
        border: 2px solid #8424e3;
        background: #f0e8ff;
        margin: 0 auto;
    }

    .vx-card .vx-name {
        margin-top: .35rem;
        font-weight: 600;
        font-size: .9rem;
    }

    .vx-card .vx-badges {
        margin-top: .15rem;
    }

    .vx-card .vx-pct {
        position: absolute;
        top: 6px;
        right: 6px;
        background: #8424e3;
        color: #fff;
        border-radius: 999px;
        padding: .1rem .35rem;
        font-size: .75rem;
    }

    /* highlight vs muted */
    .vx-card.is-primary img {
        border-color: #8424e3
    }

    .vx-card.is-primary .vx-pct {
        background: #8424e3;
        color: #fff
    }

    .vx-card.is-muted {
        filter: grayscale(1) contrast(.9) opacity(.70);
    }

    .vx-card.is-muted .vx-pct {
        background: #d2c8f2;
        color: #6b6b6b
    }

    .vx-card.is-muted .badge-primary {
        background: #d2c8f2;
        border-color: #d2c8f2;
        color: #555
    }
</style>
