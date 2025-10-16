@php
    use Illuminate\Support\Str;

    $uid = 'nxm-' . uniqid();

    // Build a JS-friendly structure and sign Wasabi URLs for ~20 minutes
$playersForJs = collect($playersByRole)->map(function ($group) {
    return collect($group)->map(function ($p) {
        // temporary Wasabi URL (20 min)
        $img = $p->image ?? '';
        $abs = Str::startsWith($img, ['http://','https://']);
        try {
            $resolved = $abs ? $img : (\Storage::disk('wasabi')->temporaryUrl($img, now()->addMinutes(20)));
        } catch (\Throwable $e) {
            $resolved = $abs ? $img : '';
        }

        // initial from LAST NAME
        $name  = trim($p->name ?? '');
        $parts = preg_split('/\s+/', $name);
        $last  = $parts ? end($parts) : $name;
        $initial = Str::upper(Str::substr($last, 0, 1) ?: '?');

        return [
            'id'              => $p->id,
            'name'            => $p->name,
            'jersey_number'   => $p->jersey_number,
            '_resolved_image' => $resolved,
            'initial'         => $initial,
        ];
    })->values();
});
@endphp


<div id="{{ $uid }}" class="container my-4">
    @if (session('ok'))
        <div class="alert alert-success mb-3">{{ session('ok') }}</div>
    @endif

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

        <form method="POST" action="{{ route('formazione.store') }}" id="{{ $uid }}-form">
            @csrf
            <input type="hidden" name="team" value="{{ $team }}">

            <div class="form-group">
                <label for="{{ $uid }}-formation">Scegli la formazione</label>
                <select class="form-control w-auto" id="{{ $uid }}-formation" name="formation" required>
                    <option value="" selected disabled>— Seleziona —</option>
                    @foreach ($formations as $f)
                        <option value="{{ $f }}">{{ $f }}</option>
                    @endforeach
                </select>
            </div>

            <div id="{{ $uid }}-pitchwrap" class="{{ $uid }}-pitch my-4"
                style="display:none; position:relative;background:#0b7a3b;border-radius:12px;padding:18px;">
                <div class="{{ $uid }}-lines"
                    style="position:absolute;inset:8px;border:2px solid rgba(255,255,255,.65);border-radius:8px;"></div>
                <div class="container-fluid position-relative" style="z-index:2;">
                    <div class="row justify-content-center mb-3" id="{{ $uid }}-row-GK"></div>
                    <div class="row justify-content-around mb-3" id="{{ $uid }}-row-DF"></div>
                    <div class="row justify-content-around mb-3" id="{{ $uid }}-row-MF"></div>
                    <div class="row justify-content-around" id="{{ $uid }}-row-FW"></div>
                </div>
            </div>


            <div class="d-flex gap-2 mt-1">
                <button type="submit" class="lv-btn" id="{{ $uid }}-submit" disabled>Salva la formazione</button>
  <button type="button" class="lv-btn-ghost" id="{{ $uid }}-clear">Svuota</button>
            </div>

            <div id="{{ $uid }}-hidden"></div>
        </form>

        {{-- Modal --}}
        <div class="modal fade" id="{{ $uid }}-modal" tabindex="-1" role="dialog"
            aria-labelledby="{{ $uid }}-label" aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="{{ $uid }}-label">
                            Seleziona un giocatore (<span id="{{ $uid }}-role"></span>)
                        </h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Chiudi">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>

                </div>
            </div>
        </div>
    @endif
</div>

<style>
.lv-btn{
  background:#8424e3; color:#fff; border:0; border-radius:0;
  text-transform:uppercase; letter-spacing:.3px; font-weight:700;
  padding:.55rem 1rem; display:inline-flex; align-items:center; gap:.35rem;
}
.lv-btn:hover{ background:#6d18c4; color:#fff; }

.lv-btn-ghost{
  background:transparent; color:#444; border:1px solid #cfcfd4; border-radius:0;
  text-transform:uppercase; font-weight:600; padding:.55rem 1rem;
}
.lv-btn-ghost:hover{ background:#f6f6f9; }

/* full-width CTA (e.g., “Più notizie”) */
.lv-btn-block{ width:100%; justify-content:center; }

/* ===== Fallback avatar (purple circle w/ initial) ===== */
.avatar-initial{
  width:64px; height:64px; border-radius:50%;
  background:#8424e3; color:#fff; display:flex; align-items:center; justify-content:center;
  font-weight:800; font-size:24px; border:2px solid #fff;
}
.avatar-initial.is-lg{
  width:72px; height:72px; font-size:26px; border-color:#8424e3;   /* modal cards */
}

    #{{ $uid }} .slot {
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

    #{{ $uid }} .slot:hover {
        transform: scale(1.02);
    }

    #{{ $uid }} .slot .avatar {
        width: 64px;
        height: 64px;
        border-radius: 50%;
        object-fit: cover;
        border: 2px solid #fff;
    }

    #{{ $uid }} .slot .label {
        font-size: .8rem;
        margin-top: .25rem;
    }

    #{{ $uid }} .badge-number {
        position: absolute;
        bottom: -10px;
        right: -10px;
        background: #8424e3;
        color: #fff;
        border-radius: 999px;
        padding: .25rem .45rem;
        font-weight: 700;
    }

    #{{ $uid }} .player-card {
        border: 1px solid #eee;
        border-radius: 10px;
        padding: .5rem;
        text-align: center;
        margin-bottom: 12px;
        cursor: pointer;
    }

    #{{ $uid }} .player-card img {
        width: 72px;
        height: 72px;
        object-fit: cover;
        border-radius: 50%;
        border: 2px solid #8424e3;
    }

    #{{ $uid }} .player-card .name {
        margin-top: .35rem;
        font-weight: 600;
    }

    #{{ $uid }} .player-card .jersey {
        margin-top: .15rem;
        display: inline-block;
        background: #8424e3;
        color: #fff;
        border-radius: 999px;
        padding: .1rem .5rem;
        font-size: .8rem;
    }

    /* pitch helper lines */
    #{{ $uid }} .{{ $uid }}-lines:before,
    #{{ $uid }} .{{ $uid }}-lines:after {
        content: '';
        position: absolute;
        left: 50%;
        transform: translateX(-50%);
        width: 70%;
        height: 2px;
        background: rgba(255, 255, 255, .45);
    }

    #{{ $uid }} .{{ $uid }}-lines:before {
        top: 25%;
    }

    #{{ $uid }} .{{ $uid }}-lines:after {
        bottom: 25%;
    }
</style>

<script>
    (function() {
        const formations = @json($formations);
        const playersByRole = @json($playersForJs);
        const UID = @json($uid);

        const $ = (sel) => document.querySelector(sel);
        const $$ = (sel) => document.querySelectorAll(sel);
        const byId = (id) => document.getElementById(id);

        const formationSelect = byId(UID + '-formation');
        const hiddenInputs = byId(UID + '-hidden');
        const submitBtn = byId(UID + '-submit');
        const clearBtn = byId(UID + '-clear');

        const modalId = '#' + UID + '-modal';
        const modalRole = byId(UID + '-role');
        const playersGrid = byId(UID + '-grid');

        let currentSlot = null;
        let expectedSlots = {};
        let selected = {}; // slot => {id,name,jersey_number,image}

        function buildExpectedSlots(f) {
            const parts = f.split('-').map(n => parseInt(n, 10));
            const mk = (pfx, n) => Object.fromEntries(Array.from({
                length: n
            }, (_, i) => [pfx + (i + 1), pfx === 'DF' ? 'DF' : pfx === 'MF' ? 'MF' : 'FW']));
            if (parts.length === 3) {
                const [d, m, f] = parts;
                return Object.assign({
                    'GK': 'GK'
                }, mk('DF', d), mk('MF', m), mk('FW', f));
            } else {
                const [d, m1, m2, f] = parts;
                const m = m1 + m2;
                return Object.assign({
                    'GK': 'GK'
                }, mk('DF', d), mk('MF', m), mk('FW', f));
            }
        }

        function rowEl(role) {
            return byId(UID + '-row-' + role);
        }

        function renderSlots() {
            ['GK', 'DF', 'MF', 'FW'].forEach(r => rowEl(r).innerHTML = '');

            Object.entries(expectedSlots).forEach(([slot, role]) => {
                const col = document.createElement('div');
                col.className = 'col-auto mb-2';

                const div = document.createElement('div');
                div.className = 'slot';
                div.dataset.slot = slot;
                div.dataset.role = role;

                if (selected[slot]) {
const s = selected[slot];
  if (s.image) {
    const img = document.createElement('img');
    img.className = 'avatar';
    img.src = s.image;
    img.alt = s.name;
    div.appendChild(img);
  } else {
    const ph = document.createElement('div');
    ph.className = 'avatar-initial';
    ph.textContent = (s.initial || '?');
    div.appendChild(ph);
  }

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

                div.addEventListener('click', () => openModal(slot, role));
                col.appendChild(div);
                rowEl(role).appendChild(col);
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
            submitBtn.disabled = (Object.keys(selected).length !== Object.keys(expectedSlots).length);
        }

        function openModal(slot, role) {
            currentSlot = slot;
            modalRole.textContent = role;
            renderPlayersGrid(playersByRole[role] || []);
            showModal(); // <-- instead of jQuery(...).modal('show')
        }

        function renderPlayersGrid(list) {
            playersGrid.innerHTML = '';
            list.forEach(p => {
                const col = document.createElement('div');
                col.className = 'col-6 col-md-4 col-lg-3';
                const card = document.createElement('div');
                card.className = 'player-card';
                card.dataset.playerId = p.id;

const hasImg = !!p._resolved_image;
if (hasImg) {
  const img = document.createElement('img');
  img.src = p._resolved_image;
  card.appendChild(img);
} else {
  const ph = document.createElement('div');
  ph.className = 'avatar-initial is-lg';
  ph.textContent = (p.initial || '?');
  card.appendChild(ph);
}


                const name = document.createElement('div');
                name.className = 'name';
                name.textContent = p.name;

                const jersey = document.createElement('div');
                jersey.className = 'jersey';
                jersey.textContent = (p.jersey_number ?? '-') + '';

                card.append(img, name, jersey);
                card.addEventListener('click', () => choosePlayer(p));
                col.appendChild(card);
                playersGrid.appendChild(col);
            });
        }

        function choosePlayer(p) {
            if (!currentSlot) return;
            Object.entries(selected).forEach(([slot, sp]) => {
                if (sp.id === p.id) delete selected[slot];
            });
            selected[currentSlot] = {
                id: p.id,
                name: p.name,
                jersey_number: p.jersey_number,
                image: p._resolved_image || ''
            };
            hideModal(); // <-- instead of jQuery(...).modal('hide')
            renderSlots();
        }

        // events
        byId(UID + '-clear').addEventListener('click', () => {
            selected = {};
            renderSlots();
        });

        const pitchWrap = document.getElementById(UID + '-pitchwrap');

        byId(UID + '-formation').addEventListener('change', (e) => {
            selected = {};
            expectedSlots = buildExpectedSlots(e.target.value);
            pitchWrap.style.display = 'block'; // <-- show pitch now
            renderSlots();
        });

        const modalElement = byId(UID + '-modal');
        let bsInstance = null;

        function showModal() {
            if (window.bootstrap && window.bootstrap.Modal) {
                bsInstance = bsInstance || new bootstrap.Modal(modalElement);
                bsInstance.show();
            } else if (window.jQuery && jQuery.fn && jQuery(modalElement).modal) {
                jQuery(modalElement).modal('show');
            } else {
                // tiny fallback
                modalElement.classList.add('show');
                modalElement.style.display = 'block';
                modalElement.removeAttribute('aria-hidden');
                document.body.classList.add('modal-open');
            }
        }

        function hideModal() {
            if (window.bootstrap && window.bootstrap.Modal) {
                (bsInstance || new bootstrap.Modal(modalElement)).hide();
            } else if (window.jQuery && jQuery.fn && jQuery(modalElement).modal) {
                jQuery(modalElement).modal('hide');
            } else {
                modalElement.classList.remove('show');
                modalElement.style.display = 'none';
                modalElement.setAttribute('aria-hidden', 'true');
                document.body.classList.remove('modal-open');
            }
        }


        // resolve image URLs if needed (no-op here but kept for parity)
        (function hydrateImages() {
            Object.keys(playersByRole).forEach(r => {
                playersByRole[r] = (playersByRole[r] || []).map(p => (p._resolved_image = p
                    ._resolved_image || p.image || '', p));
            });
        })();
    })();
</script>
