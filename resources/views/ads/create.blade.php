@extends(BaseHelper::getAdminMasterLayoutTemplate())

@section('content')
    <!-- Include CodeMirror CSS/JS if needed -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.5/codemirror.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.5/codemirror.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.5/mode/javascript/javascript.min.js"></script>

    @php
        $groups = \App\Models\AdGroup::orderBy('name')->get();
    @endphp
    <form action="{{ route('ads.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <!-- Display general errors -->
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="row">
            <!-- Main Content Column -->
            <div class="gap-3 col-md-9">
                <div class="card mb-3">
                    <div class="card-body">
                        <div class="metabox-holder columns-2" id="post-body">
                            <!-- Title Section -->
                            <div class="post-body-content">
                                <div class="mb-3" id="titlediv">
                                    <label for="title" class="form-label" id="title-prompt-text">Aggiungi titolo</label>
                                    <input type="text" class="form-control" name="post_title" id="title"
                                        value="{{ old('post_title') }}" spellcheck="true" autocomplete="off">
                                </div>
                            </div>

                            <!-- Weight Section -->
                            <div class="post-body-content">
                                <div class="mb-3">
                                    <label for="weight" class="form-label">Weight</label>
                                    <input type="text" class="form-control" name="weight" id="weight"
                                        value="{{ old('weight') }}" autocomplete="off">
                                </div>
                            </div>


                            <!-- Ad Type Selection -->
                            <div class="postbox-container" id="postbox-container-2">
                                <div class="meta-box-sortables ui-sortable" id="normal-sortables">
                                    <div class="postbox" id="ad-main-box">
                                        <div class="postbox-header">
                                            <h2 class="hndle ui-sortable-handle">Tipo Annuncio:</h2>
                                        </div>
                                        <div class="inside">
                                            <select class="form-select" name="type" id="advanced-ad-type">
                                                @foreach (\App\Models\Ad::TYPES as $key => $title)
                                                    <option value="{{ $key }}" @selected(old('type') == $key)>
                                                        {{ $title }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Image Upload Section -->
                            <div class="row mt-3 mb-3" id="imageUploadSection">
                                <div id="imageGroupSection">
                                    <label class="form-label">Image Group</label>
                                    <select class="form-select" name="ad_group_id" id="ad_group_id">
                                        <option value="">— Select image group —</option>
                                        @foreach ($groups as $g)
                                            <option value="{{ $g->id }}" @selected(old('ad_group_id', $ad->ad_group_id ?? null) == $g->id)>
                                                {{ $g->name }} ({{ $g->width }}×{{ $g->height }})
                                            </option>
                                        @endforeach
                                    </select>
                                    <small class="text-muted">Images are managed in “Ad Groups”.</small>
                                </div>
                            </div>

                            <!-- Image Name Section for Google Ad Manager -->
                            <div class="row mb-3 mt-3" id="googleAdImageNameSection" style="display: none;">
                                <div class="form-group">
                                    <label for="code">Amp</label>
                                    <textarea id="code" name="amp" class="form-control" rows="10" placeholder="Enter your amp code here">{{ old('amp') }}</textarea>
                                </div>
                            </div>


                            <div class="mt-3 mb-3">
                                <label for="vis_cond_type" class="form-label">Condizione per Visitatori</label>
                                <select id="vis_cond_type" name="vis_cond_type" class="form-select">
                                    <option value="">— Nessuna —</option>
                                    <option value="page_impressions" @selected(old('vis_cond_type') == 'page_impressions')>
                                        Impressioni pagina
                                    </option>
                                    <option value="ad_impressions" @selected(old('vis_cond_type') == 'ad_impressions')>
                                        Max Impressioni annuncio
                                    </option>
                                </select>

                                {{--   Impressioni pagina → singolo numero                           --}}
                                <input id="vis_page_input" type="number" name="vis_page_value" class="form-control mt-2"
                                    min="1" placeholder="Esempio: 3" value="{{ old('vis_page_value') }}">

                                {{--   Max impressioni annuncio → due campi (come screenshot)        --}}
                                <div id="vis_ad_inputs" class="d-flex gap-2 mt-2">
                                    <input type="number" name="vis_ad_max" class="form-control" min="1"
                                        placeholder="0" value="{{ old('vis_ad_max') }}">
                                    <span class="align-self-center">entro</span>
                                    <input type="number" name="vis_ad_seconds" class="form-control" min="1"
                                        placeholder="0" value="{{ old('vis_ad_seconds') }}">
                                    <span class="align-self-center">secondi</span>
                                </div>

                                <small class="text-muted">
                                    Visualizza l'annuncio al massimo per il valore indicato.
                                </small>
                            </div>


                            <!-- Ad Parameters Section -->
                            <div class="postbox" id="ad-parameters-box">
                                <div class="postbox-header">
                                    <h2 class="hndle ui-sortable-handle">Parametri annuncio</h2>
                                </div>
                                <div class="inside">
                                    <label for="advads-group-id" class="form-label">Gruppo annunci</label>
                                    <select class="form-select" name="group" id="advads-group-id">
                                        @foreach (\App\Models\Ad::GROUPS as $key => $title)
                                            <option value="{{ $key }}" @selected(old('group') == $key)>
                                                {{ $title }}</option>
                                        @endforeach
                                    </select>

                                    <div class="mt-3">
                                        <label for="width" class="form-label">Larghezza (px)</label>
                                        <input type="number" class="form-control" id="width" name="width"
                                            value="{{ old('width') ?? ($ad_width ?? null) }}">
                                    </div>
                                    <div class="mt-3">
                                        <label for="height" class="form-label">Altezza (px)</label>
                                        <input type="number" class="form-control" id="height" name="height"
                                            value="{{ old('height') ?? ($ad_height ?? null) }}">
                                    </div>
                                    {{-- <div class="form-check mt-3">
                                        <input class="form-check-input" type="checkbox" id="advads-wrapper-add-sizes"
                                            name="advanced_ad[output][add_wrapper_sizes]" value="true"
                                            @if (old('advanced_ad.output.add_wrapper_sizes')) checked @endif>
                                        <label class="form-check-label" for="advads-wrapper-add-sizes">Prenota questo
                                            spazio</label>
                                    </div> --}}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Sidebar Column -->
            <div class="col-md-3 gap-3 d-flex flex-column-reverse flex-md-column mb-md-0 mb-5">
                <!-- Publish Card -->
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">
                            Publish
                        </h4>
                    </div>
                    <div class="card-body">
                        <div class="btn-list">
                            <button class="btn btn-primary" type="submit" value="apply" name="submitter">
                                Save
                            </button>
                            <button class="btn" type="submit" name="submitter" value="save">
                                Save &amp; Exit
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Status Card -->
                <div class="card meta-boxes">
                    <div class="card-header">
                        <h4 class="card-title">
                            <label for="status" class="form-label required">Status</label>
                        </h4>
                    </div>
                    <div class="card-body">
                        <select class="form-control form-select" required="required" id="status" name="status"
                            aria-required="true">
                            <option value="1" @selected(old('status') == '1')>Published</option>
                            <option value="0" @selected(old('status') == '0')>Draft</option>
                        </select>
                    </div>
                </div>

                <!-- Dates Card -->
                <div class="card meta-boxes">
                    <div class="card-header">
                        <h4 class="card-title">
                            Impostazioni data
                        </h4>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label for="start_date" class="form-label">Data inizio</label>
                            <input type="date" class="form-control" name="start_date" id="start_date"
                                value="{{ old('start_date', date('Y-m-d')) }}">
                        </div>
                        <div class="mb-3">
                            <label for="expiry_date" class="form-label">Data Scadenza</label>
                            <input type="date" class="form-control" name="expiry_date" id="expiry_date"
                                value="{{ old('expiry_date') }}">
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="never_expire" name="never_expire"
                                value="1" @if (old('never_expire')) checked @endif>
                            <label class="form-check-label" for="never_expire">Scade mai</label>
                        </div>
                    </div>

                    {{-- PLACEMENT (solo mobile ads) -------------------------------------- --}}
                    <div class="mt-3 p-3">
                        <label class="form-label d-block">Placement <small>(solo mobile ads)</small></label>
                        @php
                            $placementOld = old('placement');
                        @endphp
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="placement" id="pl_home"
                                value="homepage" @checked($placementOld === 'homepage')>
                            <label class="form-check-label" for="pl_home">Homepage</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="placement" id="pl_article"
                                value="article" @checked($placementOld === 'article')>
                            <label class="form-check-label" for="pl_article">Article</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="placement" id="pl_both"
                                value="both" @checked($placementOld === 'both')>
                            <label class="form-check-label" for="pl_both">Both</label>
                        </div>
                    </div>
                </div>

            </div>

        </div>
    </form>
@endsection

@push('footer')
    <script>
        (function() {
            const input = document.getElementById('ad-label');
            const box = document.getElementById('label-suggestions');
            if (!input || !box) return;

            let controller = null;
            let last = '';

            function hideBox() {
                box.style.display = 'none';
                box.innerHTML = '';
            }

            function showBox() {
                box.style.display = 'block';
            }

            input.addEventListener('input', async (e) => {
                const q = e.target.value.trim();
                if (q === last) return;
                last = q;

                if (controller) controller.abort();
                controller = new AbortController();

                try {
                    const url = new URL('{{ route('adlabels.suggest') }}', window.location.origin);
                    if (q) url.searchParams.set('q', q);
                    const resp = await fetch(url.toString(), {
                        signal: controller.signal
                    });
                    const data = await resp.json();

                    if (!Array.isArray(data) || data.length === 0) {
                        hideBox();
                        return;
                    }

                    box.innerHTML = '';
                    data.forEach(name => {
                        const a = document.createElement('a');
                        a.href = '#';
                        a.className = 'list-group-item list-group-item-action';
                        a.textContent = name;
                        a.onclick = (ev) => {
                            ev.preventDefault();
                            input.value = name;
                            hideBox();
                        };
                        box.appendChild(a);
                    });
                    // Position box under input
                    const rect = input.getBoundingClientRect();
                    box.style.left = rect.left + 'px';
                    box.style.top = (rect.bottom + window.scrollY) + 'px';
                    box.style.width = rect.width + 'px';

                    showBox();
                } catch (err) {
                    hideBox();
                }
            });

            document.addEventListener('click', (e) => {
                if (e.target !== input && !box.contains(e.target)) hideBox();
            });
        })();
        // Switch between ad types to hide/show image upload or AMP code section.
        document.getElementById('advanced-ad-type').addEventListener('change', function(e) {
            const selectedAdType = e.target.value;
            const googleAdType = 2; // Assuming value "2" indicates Google Ad Manager
            const imageUploadSection = document.getElementById('imageUploadSection');
            const googleAdImageNameSection = document.getElementById('googleAdImageNameSection');

            if (selectedAdType == googleAdType) {
                imageUploadSection.style.display = 'none';
                googleAdImageNameSection.style.display = 'block';
            } else {
                imageUploadSection.style.display = 'block';
                googleAdImageNameSection.style.display = 'none';
            }
        });

        document.getElementById('never_expire').addEventListener('change', function() {
            const expiryField = document.getElementById('expiry_date');
            if (this.checked) {
                expiryField.disabled = true;
                expiryField.value = '';
            } else {
                expiryField.disabled = false;
            }
        });

        (function() {
            const fileInput = document.getElementById('imageUpload');
            const previewWrapper = document.getElementById('previewWrapper');

            /* keep a mutable copy of chosen files */
            let filesData = [];

            fileInput.addEventListener('change', e => {
                filesData = [...e.target.files]; // reset
                renderPreviews();
            });

            /* ---------- helper: refresh the <input type="file"> with filesData ---------- */
            function syncFileInput() {
                const dt = new DataTransfer();
                filesData.forEach(f => dt.items.add(f));
                fileInput.files = dt.files;
            }

            /* ---------- helper: (re)build the preview list ---------- */
            function renderPreviews() {
                previewWrapper.innerHTML = '';

                filesData.forEach((file, idx) => {
                    /* container */
                    const card = document.createElement('div');
                    card.className = 'border rounded p-2 position-relative';

                    /*  remove button  */
                    const removeBtn = document.createElement('button');
                    removeBtn.type = 'button';
                    removeBtn.className = 'btn-close position-absolute top-0 end-0 m-2';
                    removeBtn.title = 'Remove';
                    removeBtn.onclick = () => {
                        filesData.splice(idx, 1);
                        renderPreviews();
                        syncFileInput();
                    };
                    card.appendChild(removeBtn);

                    /* image preview (natural size) */
                    const img = document.createElement('img');
                    img.className = 'd-block mb-2';
                    img.style.maxWidth = '100%'; // respect natural width but don’t overflow column
                    img.style.height = 'auto';
                    const reader = new FileReader();
                    reader.onload = ev => {
                        img.src = ev.target.result;
                    };
                    reader.readAsDataURL(file);
                    card.appendChild(img);

                    /* url input */
                    const urlInput = document.createElement('input');
                    urlInput.type = 'url';
                    urlInput.name = `urls[${idx}]`;
                    urlInput.placeholder = 'https://example.com';
                    urlInput.required = true;
                    urlInput.className = 'form-control';
                    card.appendChild(urlInput);

                    previewWrapper.appendChild(card);
                });
            }
        })();

        // Initialize CodeMirror on the "code" textarea if it exists.
        if (document.getElementById("code")) {
            var editor = CodeMirror.fromTextArea(document.getElementById("code"), {
                lineNumbers: true,
                mode: "javascript",
                theme: "default"
            });
        }
    </script>

    <style>
        .hidden {
            display: none !important;
        }

        .block {
            display: block !important;
        }

        /* pageInput when shown   */
        .flex {
            display: flex !important;
        }

        /* adInputs when shown    */
    </style>
    <script>
        const typeSel = document.getElementById('vis_cond_type');
        const pageInput = document.getElementById('vis_page_input');
        const adInputs = document.getElementById('vis_ad_inputs');

        typeSel.addEventListener('change', toggle);
        toggle(); // run once on page-load

        function clearDisplay(el) {
            el.classList.remove('hidden', 'block', 'flex'); // wipes previous state
        }

        function toggle() {
            clearDisplay(pageInput);
            clearDisplay(adInputs);

            switch (typeSel.value) {
                case 'page_impressions':
                    pageInput.classList.add('block'); // show as block
                    adInputs.classList.add('hidden'); // hide the flex container
                    break;

                case 'ad_impressions':
                    pageInput.classList.add('hidden'); // hide the single input
                    adInputs.classList.add('flex'); // show the pair, keep it horizontal
                    break;

                default: // “— Nessuna —”
                    pageInput.classList.add('hidden');
                    adInputs.classList.add('hidden');
            }
        }
    </script>
@endpush
