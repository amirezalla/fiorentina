@php
    use Illuminate\Support\Str;

    // helper immagine (Wasabi temporanea se relativo)
    $imgUrl = function ($player) {
        if (!$player) {
            return null;
        }
        $img = (string) ($player->image ?? '');
        if (Str::startsWith($img, ['http://', 'https://'])) {
            return $img;
        }
        try {
            return \Storage::disk('wasabi')->temporaryUrl($img, now()->addMinutes(20));
        } catch (\Throwable $e) {
            return null;
        }
    };

    $slotGroups = function ($topXI) {
        $gk = [];
        $df = [];
        $mf = [];
        $fw = [];
        foreach ($topXI as $slot => $info) {
            if ($slot === 'GK') {
                $gk[$slot] = $info;
            } elseif (Str::startsWith($slot, 'DF')) {
                $df[$slot] = $info;
            } elseif (Str::startsWith($slot, 'MF')) {
                $mf[$slot] = $info;
            } elseif (Str::startsWith($slot, 'FW')) {
                $fw[$slot] = $info;
            }
        }
        return ['GK' => $gk, 'DF' => $df, 'MF' => $mf, 'FW' => $fw];
    };
@endphp

<div class="container my-3">

    {{-- ======= Formazione più votata ======= --}}
    <h4 class="mb-2">Formazione più votata</h4>

    @if ($total === 0 || !$topFormation)
        <div class="text-muted">Nessun voto registrato per questa partita.</div>
    @else
        <div class="card mb-3">
            <div class="card-body py-2">
                <div class="d-flex flex-wrap align-items-center">
                    <div class="mr-3"><strong>Voti</strong> {{ $total }}</div>
                    <div class="mr-3"><strong>Modulo</strong> {{ $topFormation }}</div>
                </div>
            </div>
        </div>

        {{-- layout semplice in righe GK / DF / MF / FW con badge percentuale --}}
        @php $rows = $slotGroups($topXI); @endphp

        @foreach (['GK' => 'Portiere', 'DF' => 'Difensori', 'MF' => 'Centrocampisti', 'FW' => 'Attaccanti'] as $key => $label)
            @if (count($rows[$key]))
                <div class="mb-2 font-weight-bold">{{ $label }}</div>
                <div class="d-flex flex-wrap mb-3">
                    @foreach ($rows[$key] as $slot => $info)
                        @php
                            $p = $info['player'];
                            $photo = $imgUrl($p);
                        @endphp
                        <div class="result-card mr-2 mb-2">
                            <div class="pct-badge">{{ $info['perc'] }}%</div>

                            @if ($photo)
                                <img src="{{ $photo }}" alt="{{ $p->name }}" class="avatar">
                            @else
                                @php
                                    $initial = Str::upper(Str::substr(Str::afterLast($p->name, ' '), 0, 1) ?: '?');
                                @endphp
                                <div class="avatar-initial">{{ $initial }}</div>
                            @endif

                            <div class="pname">
                                {{ Str::beforeLast($p->name, ' ') }}<br>
                                <strong>{{ Str::afterLast($p->name, ' ') }}</strong>
                            </div>

                            <div class="jersey">#{{ $p->jersey_number ?? '—' }}</div>
                            <div class="slot-label">{{ $slot }}</div>
                        </div>
                    @endforeach
                </div>
            @endif
        @endforeach
    @endif

    {{-- ======= Statistica Modulo ======= --}}
    <h4 class="mt-4 mb-2">Statistica Modulo</h4>
    @forelse ($formationStats as $form => $cnt)
        @php $perc = $total ? round($cnt / $total * 100) : 0; @endphp
        <div class="bar-row">
            <div class="bar" style="width:{{ max($perc, 2) }}%"></div>
            <div class="bar-label">
                <span class="pill">{{ $perc }}%</span>
                <span class="form">{{ $form }}</span>
            </div>
        </div>
    @empty
        <div class="text-muted">Nessun dato.</div>
    @endforelse

    {{-- ======= Statistiche per ruolo ======= --}}
    @foreach (['GK' => 'Statistica Portiere', 'DF' => 'Statistica Difensori', 'MF' => 'Statistica Centrocampisti', 'FW' => 'Statistica Attaccanti'] as $role => $ttl)
        <h4 class="mt-4 mb-2">{{ $ttl }}</h4>
        @forelse ($roleStats[$role] as $row)
            @php
                $p = $row['player'];
                $perc = (int) $row['perc'];
                $label = trim($p->jersey_number . ' ' . $p->name);
            @endphp
            <div class="bar-row">
                <div class="bar" style="width:{{ max($perc, 2) }}%"></div>
                <div class="bar-label">
                    <span class="pill">{{ $perc }}%</span>
                    <span class="form">{{ $label }}</span>
                </div>
            </div>
        @empty
            <div class="text-muted">Nessun dato.</div>
        @endforelse
    @endforeach
</div>

<style>
    /* card giocatore */
    .result-card {
        position: relative;
        width: 130px;
        border: 1px solid #eee;
        border-radius: 12px;
        padding: .6rem .6rem .5rem;
        text-align: center;
        background: #fff;
    }

    .result-card .avatar {
        width: 68px;
        height: 68px;
        border-radius: 50%;
        object-fit: cover;
        border: 2px solid #8424e3;
        display: block;
        margin: 0 auto .35rem;
    }

    /* fallback iniziale viola */
    .avatar-initial {
        width: 68px;
        height: 68px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        background: #8424e3;
        color: #fff;
        font-weight: 800;
        font-size: 22px;
        border: 2px solid #8424e3;
        margin: 0 auto .35rem;
    }

    /* percentuale in alto a destra */
    .pct-badge {
        position: absolute;
        top: 6px;
        right: 6px;
        background: #8424e3;
        color: #fff;
        border-radius: 999px;
        padding: .1rem .45rem;
        font-size: .8rem;
        font-weight: 700;
    }

    /* nome */
    .result-card .pname {
        line-height: 1.15;
        margin-bottom: .25rem;
    }

    /* numero maglia centrato */
    .jersey {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 34px;
        height: 22px;
        padding: 0 .5rem;
        background: #8424e3;
        color: #fff;
        border-radius: 999px;
        font-weight: 700;
        margin-bottom: .15rem;
    }

    /* slot label sotto */
    .slot-label {
        font-size: .75rem;
        color: #777;
        margin-top: .15rem;
    }

    /* righe barra (statistiche) */
    .bar-row {
        position: relative;
        background: #f3f3f6;
        border-radius: 6px;
        height: 28px;
        margin-bottom: .4rem;
        overflow: hidden;
    }

    .bar-row .bar {
        position: absolute;
        top: 0;
        left: 0;
        bottom: 0;
        background: #8424e3;
    }

    .bar-row .bar-label {
        position: relative;
        z-index: 2;
        display: flex;
        align-items: center;
        height: 28px;
        padding: 0 .5rem;
        gap: .45rem;
        font-weight: 700;
        color: #222;
    }

    .bar-row .pill {
        background: #5f1bb7;
        color: #fff;
        border-radius: 999px;
        padding: .05rem .45rem;
        font-size: .8rem;
    }

    .bar-row .form {
        font-weight: 700;
    }
</style>
