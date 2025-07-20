{{-- resources/views/ads/edit.blade.php --}}
@extends(BaseHelper::getAdminMasterLayoutTemplate())

@php
    use Illuminate\Support\Facades\Storage;

    /** ----------------------------------------------------------------
     *  Target-URL array is stored in the single “url” column as JSON.
     * ----------------------------------------------------------------*/
    $targetUrls = json_decode($ad->url, true) ?? [];

    /** ----------------------------------------------------------------
     *  Build the list of existing images (relation first, legacy fallback)
     * ----------------------------------------------------------------*/
    $existingImages = $ad->images()->orderBy('id')->get();
    if ($existingImages->isEmpty() && $ad->image) {
        // legacy single-column ad->image
        $existingImages = collect([(object) ['id' => null, 'image_url' => $ad->image]]);
    }
@endphp

@section('content')
    <form action="{{ route('ads.update', $ad->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="row">
            {{-- MAIN COLUMN ------------------------------------------------ --}}
            <div class="col-md-9 gap-3">
                <div class="card mb-3">
                    <div class="card-body">
                        {{-- TITLE --}}
                        <div class="mb-3">
                            <label for="post_title" class="form-label">Titolo</label>
                            <input name="post_title" id="post_title" type="text" class="form-control"
                                value="{{ old('post_title', $ad->title) }}">
                        </div>

                        {{-- WEIGHT --}}
                        <div class="mb-3">
                            <label for="weight" class="form-label">Weight</label>
                            <input name="weight" id="weight" type="number" class="form-control"
                                value="{{ old('weight', $ad->weight) }}">
                        </div>

                        {{-- TIPO ANNUNCIO --}}
                        <div class="mb-3">
                            <label for="advanced-ad-type" class="form-label">Tipo Annuncio</label>
                            <select name="type" id="advanced-ad-type" class="form-select">
                                @foreach (\App\Models\Ad::TYPES as $k => $v)
                                    <option value="{{ $k }}" @selected($ad->type == $k)>{{ $v }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- IMAGE UPLOAD + PREVIEW (visible only for type-1 image ads) --}}
                        <div id="imageUploadSection" class="mb-3"
                            @if ($ad->type == \App\Models\Ad::TYPE_GOOGLE_ADS) style="display:none" @endif>

                            <label class="form-label" for="imageUpload">Immagini</label>
                            <input id="imageUpload" name="images[]" type="file" accept="image/*" multiple
                                class="form-control mb-2">

                            <div id="previewWrapper" class="d-flex flex-column gap-3">
                                @foreach ($existingImages as $i => $img)
                                    @php
                                        $urlValue = $targetUrls[$i] ?? '';
                                    @endphp
                                    <div class="existing-img border rounded p-2 position-relative"
                                        data-id="{{ $img->id }}">
                                        <button type="button" title="Rimuovi"
                                            class="btn-close position-absolute top-0 end-0 m-2 remove-btn"></button>

                                        <img src="{{ Storage::disk('wasabi')->temporaryUrl($img->image_url, now()->addMinutes(1500)) ?:
                                            Storage::disk('wasabi')->url($img->image_url) }}"
                                            style="max-width:100%;height:auto" class="d-block mb-2">

                                        <input name="urls_existing[{{ $img->id ?? 'single' }}]" type="url"
                                            class="form-control" placeholder="https://example.com"
                                            value="{{ old("urls_existing.$img->id", $urlValue) }}">
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        {{-- AMP textarea (visible only for Google-ad type-2) --}}
                        <div id="googleAdAmpSection" class="mb-3"
                            @if ($ad->type == \App\Models\Ad::TYPE_ANNUNCIO_IMMAGINE) style="display:none" @endif>
                            <label for="amp" class="form-label">AMP Code</label>
                            <textarea id="amp" name="amp" rows="10" class="form-control" placeholder="Enter your AMP code here">{{ old('amp', $ad->amp) }}</textarea>
                        </div>

                        {{-- GROUP --}}
                        <div class="mb-3">
                            <label for="group" class="form-label">Gruppo annunci</label>
                            <select name="group" id="group" class="form-select">
                                @foreach (\App\Models\Ad::GROUPS as $k => $v)
                                    <option value="{{ $k }}" @selected($ad->group == $k)>{{ $v }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- SIZE --}}
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="width" class="form-label">Larghezza (px)</label>
                                <input name="width" id="width" type="number" class="form-control"
                                    value="{{ old('width', $ad->width) }}">
                            </div>
                            <div class="col-md-6">
                                <label for="height" class="form-label">Altezza (px)</label>
                                <input name="height" id="height" type="number" class="form-control"
                                    value="{{ old('height', $ad->height) }}">
                            </div>
                        </div>

                        {{-- DATES --}}
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="start_date" class="form-label">Data inizio</label>
                                <input name="start_date" id="start_date" type="date" class="form-control"
                                    value="{{ old('start_date', $ad->start_date ?? date('Y-m-d')) }}">
                            </div>
                            <div class="col-md-6">
                                <label for="expiry_date" class="form-label">Data scadenza</label>
                                <input name="expiry_date" id="expiry_date" type="date" class="form-control"
                                    value="{{ old('expiry_date', $ad->expiry_date) }}">
                            </div>
                        </div>

                        {{-- STATUS --}}
                        <div class="mb-4">
                            <label for="status" class="form-label">Status</label>
                            <select name="status" id="status" class="form-select">
                                <option value="1" @selected($ad->status == 1)>Published</option>
                                <option value="0" @selected($ad->status == 0)>Draft</option>
                            </select>
                        </div>

                        <button class="btn btn-primary" type="submit">Aggiorna</button>
                    </div> {{-- .card-body --}}
                </div> {{-- .card --}}
            </div> {{-- .col-md-9 --}}
            {{-- SIDEBAR left empty on purpose --}}
        </div> {{-- .row --}}
        @php
            $visType = old('vis_cond_type', $ad->vis_cond_type);
        @endphp
        <div class="mb-3">
            <label for="vis_cond_type" class="form-label">Condizione per Visitatori</label>
            <select id="vis_cond_type" name="vis_cond_type" class="form-select">
                <option value="">— Nessuna —</option>
                <option value="page_impressions" @selected($visType == 'page_impressions')>Impressioni pagina</option>
                <option value="ad_impressions" @selected($visType == 'ad_impressions')>Max Impressioni annuncio</option>
            </select>

            <input id="vis_page_input" type="number" name="vis_page_value" class="form-control mt-2"
                value="{{ old('vis_page_value', $ad->vis_page_value) }}" style="display:none">

            <div id="vis_ad_inputs" class="d-flex gap-2 mt-2" style="display:none">
                <input type="number" name="vis_ad_max" class="form-control"
                    value="{{ old('vis_ad_max', $ad->vis_ad_max) }}">
                <span class="align-self-center">entro</span>
                <input type="number" name="vis_ad_seconds" class="form-control"
                    value="{{ old('vis_ad_seconds', $ad->vis_ad_seconds) }}">
                <span class="align-self-center">secondi</span>
            </div>
        </div>

        {{-- PLACEMENT --------------------------------------------------------- --}}
        @php  $pl = old('placement', $ad->placement);  @endphp
        <div class="mb-3">
            <label class="form-label d-block">Placement <small>(solo mobile ads)</small></label>
            <div class="form-check">
                <input class="form-check-input" type="radio" name="placement" id="pl_home" value="homepage"
                    @checked($pl === 'homepage')>
                <label class="form-check-label" for="pl_home">Homepage</label>
            </div>
            <div class="form-check">
                <input class="form-check-input" type="radio" name="placement" id="pl_article" value="article"
                    @checked($pl === 'article')>
                <label class="form-check-label" for="pl_article">Article</label>
            </div>
            <div class="form-check">
                <input class="form-check-input" type="radio" name="placement" id="pl_both" value="both"
                    @checked($pl === 'both')>
                <label class="form-check-label" for="pl_both">Both</label>
            </div>
        </div>

        {{-- Will hold a comma-separated list of existing-image IDs the user deleted --}}
        <input type="hidden" name="deleted_images" id="deleted_images" value="">
    </form>
@endsection


@push('footer')
    {{-- CodeMirror (optional pretty editor for AMP textarea) --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.5/codemirror.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.5/codemirror.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.5/mode/javascript/javascript.min.js"></script>

    <script>
        (function() {
            /* ---------- DOM references ---------- */
            const typeSelect = document.getElementById('advanced-ad-type');
            const imgSection = document.getElementById('imageUploadSection');
            const ampSection = document.getElementById('googleAdAmpSection');
            const fileInput = document.getElementById('imageUpload');
            const previewWrapper = document.getElementById('previewWrapper');
            const deletedField = document.getElementById('deleted_images');

            /* ---------- state ---------- */
            let newFiles = []; // <File> objects chosen during this session
            const deletedExisting = new Set(); // ids of already-stored AdImage rows

            /* ---------- helpers ---------- */
            function syncFileInput() {
                const dt = new DataTransfer();
                newFiles.forEach(f => dt.items.add(f));
                fileInput.files = dt.files;
            }

            function renderNewPreviews() {
                previewWrapper.querySelectorAll('.new-img').forEach(el => el.remove());

                newFiles.forEach((file, idx) => {
                    const card = document.createElement('div');
                    card.className = 'new-img border rounded p-2 position-relative mt-2';

                    /* remove button */
                    const btn = document.createElement('button');
                    btn.type = 'button';
                    btn.className = 'btn-close position-absolute top-0 end-0 m-2';
                    btn.onclick = () => {
                        newFiles.splice(idx, 1);
                        renderNewPreviews();
                        syncFileInput();
                    };
                    card.appendChild(btn);

                    /* preview image */
                    const img = document.createElement('img');
                    img.style.maxWidth = '100%';
                    img.style.height = 'auto';
                    new FileReader().addEventListener('load', e => img.src = e.target.result);
                    new FileReader().readAsDataURL?.(file);
                    card.appendChild(img);

                    /* target url */
                    const url = document.createElement('input');
                    url.type = 'url';
                    url.name = `urls_new[${idx}]`;
                    url.required = true;
                    url.className = 'form-control mt-2';
                    url.placeholder = 'https://example.com';
                    card.appendChild(url);

                    previewWrapper.appendChild(card);
                });
            }

            /* ---------- events ---------- */
            typeSelect.addEventListener('change', e => {
                const isGoogle = +e.target.value === {{ \App\Models\Ad::TYPE_GOOGLE_ADS }};
                imgSection.style.display = isGoogle ? 'none' : 'block';
                ampSection.style.display = isGoogle ? 'block' : 'none';
            });

            document.querySelectorAll('.existing-img .remove-btn').forEach(btn => {
                btn.addEventListener('click', () => {
                    const card = btn.closest('.existing-img');
                    if (card.dataset.id) deletedExisting.add(card.dataset.id);
                    card.remove();
                    deletedField.value = [...deletedExisting].join(',');
                });
            });

            fileInput.addEventListener('change', e => {
                newFiles = [...e.target.files];
                renderNewPreviews();
                syncFileInput();
            });

            if (document.getElementById('amp')) {
                CodeMirror.fromTextArea(
                    document.getElementById('amp'), {
                        lineNumbers: true,
                        mode: 'javascript',
                        theme: 'default'
                    }
                );
            }
        })();
    </script>
    <script>
        (() => {
            const typeSel = document.getElementById('vis_cond_type');
            const pageInput = document.getElementById('vis_page_input');
            const adInputs = document.getElementById('vis_ad_inputs');

            function toggle() {
                const v = typeSel.value;
                pageInput.style.display = v === 'page_impressions' ? 'block' : 'none';
                adInputs.style.display = v === 'ad_impressions' ? 'flex' : 'none';
            }
            typeSel.addEventListener('change', toggle);
            toggle(); // run once at load
        })();
    </script>
@endpush
