@php
    // $match (or null), $playersByRole (collection), $formations (array), $team = 'fiorentina'
    use Illuminate\Support\Str;

    function playerImageUrl($p)
    {
        // Prefer absolute, else use Wasabi helper
        if (!empty($p->image) && Str::startsWith($p->image, ['http://', 'https://'])) {
            return $p->image;
        }
        try {
            return \Storage::disk('wasabi')->url($p->image);
        } catch (\Throwable $e) {
            return null;
        }
    }
@endphp

@section('content')
    <div class="container my-4">

        <div class="mb-3">
            @if (session('ok'))
                <div class="alert alert-success">{{ session('ok') }}</div>
            @endif
        </div>

        <h2 class="mb-2">Vota la tua formazione</h2>

        @if (!$match)
            <div class="alert alert-info">Nessuna partita imminente disponibile per la votazione.</div>
        @else
            @php
                $home_team = json_decode($match->home_team ?? '{}', true);
                $away_team = json_decode($match->away_team ?? '{}', true);
            @endphp
            <div class="d-flex align-items-center gap-3 mb-3">
                <img src="{{ $home_team['logo'] ?? '' }}" alt="" style="height:30px">
                <strong class="mx-1">{{ $home_team['name'] ?? '' }}</strong>
                <span class="mx-2">vs</span>
                <img src="{{ $away_team['logo'] ?? '' }}" alt="" style="height:30px">
                <strong class="mx-1">{{ $away_team['name'] ?? '' }}</strong>
                <span class="ml-3 text-muted">
                    {{ \Carbon\Carbon::parse($match->match_date)->locale('it')->timezone('Europe/Rome')->isoFormat('dddd D MMMM [ore] H:mm') }}
                </span>
            </div>

            <form method="POST" action="{{ route('formazione.store') }}" id="formationForm">
                @csrf
                <input type="hidden" name="team" value="{{ $team }}">

                <!-- formation selector -->
                <div class="form-group">
                    <label for="formation">Scegli la formazione</label>
                    <select class="form-control w-auto" id="formation" name="formation" required>
                        <option value="" selected disabled>— Seleziona —</option>
                        @foreach ($formations as $f)
                            <option value="{{ $f }}">{{ $f }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Pitch -->
                <div class="football-pitch my-4"
                    style="position:relative;background:#0b7a3b;border-radius:12px;padding:18px;">
                    <div class="pitch-lines"
                        style="position:absolute;inset:8px;border:2px solid rgba(255,255,255,.65);border-radius:8px;"></div>

                    <div id="slotsWrap" class="container-fluid position-relative" style="z-index:2;">
                        <!-- GK row -->
                        <div class="row justify-content-center mb-3" id="row-GK"></div>
                        <!-- DF row -->
                        <div class="row justify-content-around mb-3" id="row-DF"></div>
                        <!-- MF row -->
                        <div class="row justify-content-around mb-3" id="row-MF"></div>
                        <!-- FW row -->
                        <div class="row justify-content-around" id="row-FW"></div>
                    </div>
                </div>

                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary" id="submitBtn" disabled>Salva la formazione</button>
                    <button type="button" class="btn btn-outline-secondary" id="clearBtn">Svuota</button>
                </div>

                <!-- hidden inputs go here -->
                <div id="hiddenInputs"></div>
            </form>

            <!-- Modal -->
            <div class="modal fade" id="playersModal" tabindex="-1" role="dialog" aria-labelledby="playersModalLabel"
                aria-hidden="true">
                <div class="modal-dialog modal-lg" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">
                                Seleziona un giocatore (<span id="modalRole"></span>)
                            </h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Chiudi">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">

                            <input type="text" id="playerSearch" class="form-control mb-3" placeholder="Cerca...">

                            <div id="playersGrid" class="row">
                                {{-- filled by JS --}}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>
@endsection

@push('styles')
    <style>
        .slot {
            width: 96px;
            height: 96px;
            background: rgba(255, 255, 255, .12);
            border: 2px dashed rgba(255, 255, 255, .6);
            border-radius: 12px;
            color: #fff;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            position: relative;
            transition: transform .05s ease-in;
        }

        .slot:hover {
            transform: scale(1.02);
        }

        .slot .avatar {
            width: 64px;
            height: 64px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid #fff;
        }

        .slot .label {
            font-size: .8rem;
            margin-top: .25rem;
        }

        .badge-number {
            position: absolute;
            bottom: -10px;
            right: -10px;
            background: #8424e3;
            color: #fff;
            border-radius: 999px;
            padding: .25rem .45rem;
            font-weight: 700;
        }

        .player-card {
            border: 1px solid #eee;
            border-radius: 10px;
            padding: .5rem;
            text-align: center;
            margin-bottom: 12px;
            cursor: pointer;
        }

        .player-card img {
            width: 72px;
            height: 72px;
            object-fit: cover;
            border-radius: 50%;
            border: 2px solid #8424e3;
        }

        .player-card .name {
            margin-top: .35rem;
            font-weight: 600;
        }

        .player-card .jersey {
            margin-top: .15rem;
            display: inline-block;
            background: #8424e3;
            color: #fff;
            border-radius: 999px;
            padding: .1rem .5rem;
            font-size: .8rem;
        }

        .pitch-lines:before,
        .pitch-lines:after {
            content: '';
            position: absolute;
            left: 50%;
            transform: translateX(-50%);
            width: 70%;
            height: 2px;
            background: rgba(255, 255, 255, .45);
        }

        .pitch-lines:before {
            top: 25%;
        }

        .pitch-lines:after {
            bottom: 25%;
        }
    </style>
@endpush

@push('scripts')
    <script>
        (function() {
            const formations = @json($formations);
            const playersByRole = @json($playersByRole->map->values()); // {GK:[...], DF:[...], MF:[...], FW:[...]}

            const formationSelect = document.getElementById('formation');
            const hiddenInputs = document.getElementById('hiddenInputs');
            const submitBtn = document.getElementById('submitBtn');
            const clearBtn = document.getElementById('clearBtn');

            /* modal elements */
            const modalEl = document.getElementById('playersModal');
            const modalRole = document.getElementById('modalRole');
            const playersGrid = document.getElementById('playersGrid');
            const playerSearch = document.getElementById('playerSearch');

            let currentSlot = null; // e.g., 'DF2'
            let expectedSlots = {}; // map slot => role
            let selected = {}; // slot => {id, name, jersey_number, image}

            function buildExpectedSlots(f) {
                const parts = f.split('-').map(x => parseInt(x, 10));
                const def = (n) => [...Array(n).keys()].map(i => `DF${i+1}`);
                const mid = (n) => [...Array(n).keys()].map(i => `MF${i+1}`);
                const fwd = (n) => [...Array(n).keys()].map(i => `FW${i+1}`);

                if (parts.length === 3) {
                    const [d, m, fw] = parts;
                    return {
                        'GK': 'GK',
                        ...Object.fromEntries(def(d).map(k => [k, 'DF'])),
                        ...Object.fromEntries(mid(m).map(k => [k, 'MF'])),
                        ...Object.fromEntries(fwd(fw).map(k => [k, 'FW']))
                    };
                } else {
                    const [d, m1, m2, fw] = parts;
                    const totalM = m1 + m2;
                    return {
                        'GK': 'GK',
                        ...Object.fromEntries(def(d).map(k => [k, 'DF'])),
                        ...Object.fromEntries(mid(totalM).map(k => [k, 'MF'])),
                        ...Object.fromEntries(fwd(fw).map(k => [k, 'FW']))
                    };
                }
            }

            function renderSlots() {
                ['GK', 'DF', 'MF', 'FW'].forEach(role => {
                    const row = document.getElementById('row-' + role);
                    row.innerHTML = '';
                });

                Object.entries(expectedSlots).forEach(([slot, role]) => {
                    const row = document.getElementById('row-' + role);
                    const col = document.createElement('div');
                    col.className = 'col-auto mb-2';

                    const div = document.createElement('div');
                    div.className = 'slot';
                    div.dataset.slot = slot;
                    div.dataset.role = role;

                    if (selected[slot]) {
                        const img = document.createElement('img');
                        img.className = 'avatar';
                        img.src = selected[slot].image || '';
                        img.alt = selected[slot].name;
                        div.appendChild(img);

                        const name = document.createElement('div');
                        name.className = 'label';
                        name.textContent = selected[slot].name;
                        div.appendChild(name);

                        const num = document.createElement('div');
                        num.className = 'badge-number';
                        num.textContent = selected[slot].jersey_number ?? '-';
                        div.appendChild(num);
                    } else {
                        const label = document.createElement('div');
                        label.className = 'label';
                        label.textContent = slot;
                        div.appendChild(label);
                    }

                    div.addEventListener('click', () => openModalFor(slot, role));
                    col.appendChild(div);
                    row.appendChild(col);
                });

                syncHiddenInputs();
                updateSubmitState();
            }

            function syncHiddenInputs() {
                hiddenInputs.innerHTML = '';
                Object.entries(selected).forEach(([slot, p]) => {
                    const input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = `positions[${slot}]`;
                    input.value = p.id;
                    hiddenInputs.appendChild(input);
                });
            }

            function updateSubmitState() {
                const totalSlots = Object.keys(expectedSlots).length;
                const filled = Object.keys(selected).length;
                submitBtn.disabled = (filled !== totalSlots);
            }

            function openModalFor(slot, role) {
                currentSlot = slot;
                modalRole.textContent = role;
                playerSearch.value = '';
                renderPlayersGrid(playersForRole(role));
                // Bootstrap open
                $('#playersModal').modal('show');
            }

            function playersForRole(role) {
                return (playersByRole[role] || []);
            }

            function renderPlayersGrid(list) {
                playersGrid.innerHTML = '';
                list.forEach(p => {
                    const col = document.createElement('div');
                    col.className = 'col-6 col-md-4 col-lg-3';
                    const card = document.createElement('div');
                    card.className = 'player-card';
                    card.dataset.playerId = p.id;

                    const img = document.createElement('img');
                    img.src = p.image_url ?? p.image ?? '';
                    if (!img.src) img.src = '';
                    // server already resolves absolute via PHP below
                    img.src = p._resolved_image || img.src;

                    const name = document.createElement('div');
                    name.className = 'name';
                    name.textContent = p.name;

                    const jersey = document.createElement('div');
                    jersey.className = 'jersey';
                    jersey.textContent = (p.jersey_number ?? '-') + '';

                    card.appendChild(img);
                    card.appendChild(name);
                    card.appendChild(jersey);
                    card.addEventListener('click', () => choosePlayer(p));
                    col.appendChild(card);
                    playersGrid.appendChild(col);
                });
            }

            function choosePlayer(p) {
                if (!currentSlot) return;
                // prevent the same player in multiple slots: remove if already used
                Object.entries(selected).forEach(([slot, sp]) => {
                    if (sp.id === p.id) delete selected[slot];
                });

                // resolve image (server-provided absolute in data attr)
                const resolved = {
                    id: p.id,
                    name: p.name,
                    jersey_number: p.jersey_number,
                    image: p._resolved_image || p.image || '',
                };
                selected[currentSlot] = resolved;
                $('#playersModal').modal('hide');
                renderSlots();
            }

            formationSelect.addEventListener('change', () => {
                selected = {};
                expectedSlots = buildExpectedSlots(formationSelect.value);
                renderSlots();
            });

            clearBtn.addEventListener('click', () => {
                selected = {};
                renderSlots();
            });

            // Prime players array with resolved image URLs from server (avoids mixed content / missing)
            (function hydrateResolvedImages() {
                Object.keys(playersByRole).forEach(role => {
                    playersByRole[role] = (playersByRole[role] || []).map(p => {
                        p._resolved_image = {!! json_encode('') !!};
                        return p;
                    });
                });
            })();

        })();
    </script>
@endpush
