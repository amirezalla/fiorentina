{{-- resources/views/ads/edit.blade.php --}}
@extends(BaseHelper::getAdminMasterLayoutTemplate())

@section('content')
    {{-- CodeMirror (optional, for AMP textarea prettiness) --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.5/codemirror.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.5/codemirror.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.5/mode/javascript/javascript.min.js"></script>

    @php
        /** Image groups (shared assets) **/
        $imageGroups = \App\Models\AdGroup::orderBy('name')->get();
        $isGoogle = (int) old('type', $ad->type) === \App\Models\Ad::TYPE_GOOGLE_ADS;
    @endphp

    {{-- SHOW ALL VALIDATION ERRORS --}}
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $e)
                    <li>{{ $e }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('ads.update', $ad->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="row">
            {{-- =================== MAIN COLUMN =================== --}}
            <div class="col-md-9 gap-3">
                <div class="card mb-3">
                    <div class="card-body">
                        <div class="metabox-holder columns-2" id="post-body">
                            {{-- TITLE --}}
                            <div class="post-body-content">
                                <div class="mb-3" id="titlediv">
                                    <label for="title" class="form-label">Aggiungi titolo</label>
                                    <input type="text" class="form-control" name="post_title" id="title"
                                        value="{{ old('post_title', $ad->title) }}" spellcheck="true" autocomplete="off">
                                </div>
                            </div>

                            {{-- WEIGHT (0..10 step 1) --}}
                            <div class="post-body-content">
                                <div class="mb-3">
                                    <label for="weight" class="form-label">Weight</label>
                                    <input type="number" min="0" max="10" step="1" class="form-control"
                                        name="weight" id="weight" value="{{ old('weight', (string) $ad->weight) }}"
                                        autocomplete="off">
                                </div>
                            </div>

                            {{-- TIPO ANNUNCIO --}}
                            <div class="postbox-container" id="postbox-container-2">
                                <div class="meta-box-sortables ui-sortable" id="normal-sortables">
                                    <div class="postbox" id="ad-main-box">
                                        <div class="postbox-header">
                                            <h2 class="hndle ui-sortable-handle">Tipo Annuncio:</h2>
                                        </div>
                                        <div class="inside">
                                            <select class="form-select" name="type" id="advanced-ad-type">
                                                @foreach (\App\Models\Ad::TYPES as $key => $title)
                                                    <option value="{{ $key }}" @selected((int) old('type', $ad->type) === (int) $key)>
                                                        {{ $title }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- IMAGE GROUP (for image ads) --}}
                            <div class="row mt-3 mb-3" id="imageGroupSection"
                                @if ($isGoogle) style="display:none" @endif>
                                <label class="form-label">Image Group</label>
                                <select class="form-select" name="ad_group_id" id="ad_group_id">
                                    <option value="">— Select image group —</option>
                                    @foreach ($imageGroups as $g)
                                        <option value="{{ $g->id }}" @selected((int) old('ad_group_id', (int) $ad->ad_group_id) === (int) $g->id)>
                                            {{ $g->name }} ({{ $g->width }}×{{ $g->height }})
                                        </option>
                                    @endforeach
                                </select>
                                <small class="text-muted">Images are managed in “Ad Groups”.</small>
                            </div>

                            {{-- AMP CODE (for Google Ad Manager) --}}
                            <div class="row mb-3 mt-3" id="googleAdAmpSection"
                                @if (!$isGoogle) style="display:none" @endif>
                                <div class="form-group">
                                    <label for="code">Amp</label>
                                    <textarea id="code" name="amp" class="form-control" rows="10" placeholder="Enter your amp code here">{{ old('amp', $ad->amp) }}</textarea>
                                </div>
                            </div>

                            {{-- CONDIZIONE PER VISITATORI --}}
                            <div class="mt-3 mb-3">
                                @php
                                    // derive current visualization settings from JSON if old() is empty
                                    $vis = $ad->visualization();
                                    $visTypeOld = old('vis_cond_type');
                                    $visType = $visTypeOld ?? ($vis['type'] ?? '');
                                @endphp

                                <label for="vis_cond_type" class="form-label">Condizione per Visitatori</label>
                                <select id="vis_cond_type" name="vis_cond_type" class="form-select">
                                    <option value="">— Nessuna —</option>
                                    <option value="page_impressions" @selected($visType === 'page')>Impressioni pagina
                                    </option>
                                    <option value="ad_impressions" @selected($visType === 'ad')>Max Impressioni annuncio
                                    </option>
                                </select>

                                {{--   Impressioni pagina → singolo numero                           --}}
                                <input id="vis_page_input" type="number" name="vis_page_value" class="form-control mt-2"
                                    min="1" placeholder="Esempio: 3"
                                    value="{{ old('vis_page_value', $vis['max'] ?? '') }}">

                                {{--   Max impressioni annuncio → due campi                          --}}
                                <div id="vis_ad_inputs" class="d-flex gap-2 mt-2">
                                    <input type="number" name="vis_ad_max" class="form-control" min="1"
                                        placeholder="0" value="{{ old('vis_ad_max', $vis['max'] ?? '') }}">
                                    <span class="align-self-center">entro</span>
                                    <input type="number" name="vis_ad_seconds" class="form-control" min="1"
                                        placeholder="0" value="{{ old('vis_ad_seconds', $vis['seconds'] ?? '') }}">
                                    <span class="align-self-center">secondi</span>
                                </div>

                                <small class="text-muted">Visualizza l'annuncio al massimo per il valore indicato.</small>
                            </div>

                            {{-- PARAMETRI ANNUNCIO --}}
                            <div class="postbox" id="ad-parameters-box">
                                <div class="postbox-header">
                                    <h2 class="hndle ui-sortable-handle">Parametri annuncio</h2>
                                </div>
                                <div class="inside">
                                    <label for="advads-group-id" class="form-label">Gruppo annunci</label>
                                    <select class="form-select" name="group" id="advads-group-id">
                                        @foreach (\App\Models\Ad::GROUPS as $key => $title)
                                            <option value="{{ $key }}" @selected((int) old('group', (int) $ad->group) === (int) $key)>
                                                {{ $title }}
                                            </option>
                                        @endforeach
                                    </select>

                                    <div class="mt-3">
                                        <label for="width" class="form-label">Larghezza (px)</label>
                                        <input type="number" class="form-control" id="width" name="width"
                                            value="{{ old('width', $ad->width) }}">
                                    </div>
                                    <div class="mt-3">
                                        <label for="height" class="form-label">Altezza (px)</label>
                                        <input type="number" class="form-control" id="height" name="height"
                                            value="{{ old('height', $ad->height) }}">
                                    </div>
                                </div>
                            </div>
                        </div> {{-- /#post-body --}}
                    </div> {{-- /.card-body --}}
                </div> {{-- /.card --}}
            </div>

            {{-- =================== SIDEBAR =================== --}}
            <div class="col-md-3 gap-3 d-flex flex-column-reverse flex-md-column mb-md-0 mb-5">

                {{-- Publish --}}
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Publish</h4>
                    </div>
                    <div class="card-body">
                        <div class="btn-list">
                            <button class="btn btn-primary" type="submit" value="apply" name="submitter">Save</button>
                            <button class="btn" type="submit" name="submitter" value="save">Save &amp;
                                Exit</button>
                        </div>
                    </div>
                </div>

                {{-- Status --}}
                <div class="card meta-boxes">
                    <div class="card-header">
                        <h4 class="card-title">
                            <label for="status" class="form-label required">Status</label>
                        </h4>
                    </div>
                    <div class="card-body">
                        <select class="form-control form-select" required id="status" name="status"
                            aria-required="true">
                            <option value="1" @selected(old('status', (string) $ad->status) === '1')>Published</option>
                            <option value="0" @selected(old('status', (string) $ad->status) === '0')>Draft</option>
                        </select>
                    </div>
                </div>

                {{-- Date settings + Placement --}}
                <div class="card meta-boxes">
                    <div class="card-header">
                        <h4 class="card-title">Impostazioni data</h4>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label for="start_date" class="form-label">Data inizio</label>
                            <input type="date" class="form-control" name="start_date" id="start_date"
                                value="{{ old('start_date', $ad->start_date ? \Illuminate\Support\Str::of($ad->start_date)->substr(0, 10) : date('Y-m-d')) }}">
                        </div>
                        <div class="mb-3">
                            <label for="expiry_date" class="form-label">Data Scadenza</label>
                            <input type="date" class="form-control" name="expiry_date" id="expiry_date"
                                value="{{ old('expiry_date', $ad->expiry_date ? \Illuminate\Support\Str::of($ad->expiry_date)->substr(0, 10) : '') }}">
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="never_expire" name="never_expire"
                                value="1" @checked(old('never_expire'))>
                            <label class="form-check-label" for="never_expire">Scade mai</label>
                        </div>
                    </div>

                    {{-- Placement (solo mobile ads) --}}
                    <div class="mt-3 p-3">
                        @php $pl = old('placement', $ad->placement); @endphp
                        <label class="form-label d-block">Placement <small>(solo mobile ads)</small></label>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="placement" id="pl_home"
                                value="homepage" @checked($pl === 'homepage')>
                            <label class="form-check-label" for="pl_home">Homepage</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="placement" id="pl_article"
                                value="article" @checked($pl === 'article')>
                            <label class="form-check-label" for="pl_article">Article</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="placement" id="pl_both"
                                value="both" @checked($pl === 'both')>
                            <label class="form-check-label" for="pl_both">Both</label>
                        </div>
                    </div>
                </div>
            </div>
        </div> {{-- /.row --}}
    </form>
@endsection

@push('footer')
    <script>
        // Toggle Image Group vs AMP
        (function() {
            const typeSel = document.getElementById('advanced-ad-type');
            const imgGroup = document.getElementById('imageGroupSection');
            const ampBox = document.getElementById('googleAdAmpSection');

            function toggle() {
                const isGoogle = +typeSel.value === {{ \App\Models\Ad::TYPE_GOOGLE_ADS }};
                imgGroup.style.display = isGoogle ? 'none' : 'block';
                ampBox.style.display = isGoogle ? 'block' : 'none';
            }

            typeSel.addEventListener('change', toggle);
            toggle();
        })();

        // Never expire toggle
        document.getElementById('never_expire').addEventListener('change', function() {
            const expiry = document.getElementById('expiry_date');
            if (this.checked) {
                expiry.value = '';
                expiry.disabled = true;
            } else {
                expiry.disabled = false;
            }
        });

        // Visualization condition toggles
        (function() {
            const typeSel = document.getElementById('vis_cond_type');
            const pageInput = document.getElementById('vis_page_input');
            const adInputs = document.getElementById('vis_ad_inputs');

            function toggle() {
                const v = typeSel.value;
                pageInput.style.display = (v === 'page_impressions' || v === 'page') ? 'block' : 'none';
                adInputs.style.display = (v === 'ad_impressions' || v === 'ad') ? 'flex' : 'none';
            }
            typeSel.addEventListener('change', toggle);
            toggle();
        })();

        // CodeMirror for AMP
        (function() {
            const ta = document.getElementById('code');
            if (!ta) return;
            CodeMirror.fromTextArea(ta, {
                lineNumbers: true,
                mode: 'javascript',
                theme: 'default'
            });
        })();
    </script>
@endpush
