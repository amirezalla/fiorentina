{{-- resources/views/ads/edit.blade.php --}}
@extends(BaseHelper::getAdminMasterLayoutTemplate())

@php
    use Illuminate\Support\Facades\Storage;
    /* decode the JSON column once so we can reuse it */
    $urls = json_decode($ad->urls, true) ?? [];
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
                            <input type="text" class="form-control" name="post_title" id="post_title"
                                value="{{ old('post_title', $ad->title) }}">
                        </div>

                        {{-- WEIGHT --}}
                        <div class="mb-3">
                            <label for="weight" class="form-label">Weight</label>
                            <input type="text" class="form-control" name="weight" id="weight"
                                value="{{ old('weight', $ad->weight) }}">
                        </div>

                        {{-- TIPO ANNUNCIO --}}
                        <div class="mb-3">
                            <label for="advanced-ad-type" class="form-label">Tipo Annuncio</label>
                            <select class="form-select" name="type" id="advanced-ad-type">
                                @foreach (\App\Models\Ad::TYPES as $key => $title)
                                    <option value="{{ $key }}" @selected($ad->type == $key)>{{ $title }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- IMAGE UPLOAD + PREVIEWS (visible only for type 1) --}}
                        <div id="imageUploadSection" class="mb-3"
                            @if ($ad->type == 2) style="display:none;" @endif>

                            {{-- new uploads go here --}}
                            <label class="form-label" for="imageUpload">Immagini</label>
                            <input type="file" id="imageUpload" name="images[]" accept="image/*" multiple
                                class="form-control mb-2">

                            {{-- previews – existing + new --}}
                            <div id="previewWrapper" class="d-flex flex-column gap-3">
                                {{-- EXISTING IMAGES --}}
                                @foreach ($ad->images as $i => $img)
                                    <div class="existing-img border rounded p-2 position-relative"
                                        data-id="{{ $img->id }}">
                                        <button type="button"
                                            class="btn-close position-absolute top-0 end-0 m-2 remove-btn"
                                            title="Remove"></button>

                                        <img src="{{ Storage::disk('wasabi')->temporaryUrl($img->image_url, now()->addMinutes(15)) }}"
                                            style="max-width:100%;height:auto" class="d-block mb-2">

                                        <input type="url" class="form-control" name="urls_existing[{{ $img->id }}]"
                                            placeholder="https://example.com" value="{{ $urls[$i] ?? '' }}">
                                    </div>
                                @endforeach
                            </div> {{-- /previewWrapper --}}
                        </div>

                        {{-- AMP FIELD (visible only for type 2) --}}
                        <div id="googleAdImageNameSection" class="mb-3"
                            @if ($ad->type == 1) style="display:none;" @endif>
                            <label for="amp" class="form-label">AMP Code</label>
                            <textarea name="amp" id="amp" rows="10" class="form-control" placeholder="Enter your AMP code here">{{ old('amp', $ad->amp) }}</textarea>
                        </div>

                        {{-- GROUP --}}
                        <div class="mb-3">
                            <label for="group" class="form-label">Gruppo annunci</label>
                            <select name="group" id="group" class="form-select">
                                @foreach (\App\Models\Ad::GROUPS as $key => $title)
                                    <option value="{{ $key }}" @selected($ad->group == $key)>{{ $title }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- SIZE --}}
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="width" class="form-label">Larghezza (px)</label>
                                <input type="number" id="width" name="width" class="form-control"
                                    value="{{ old('width', $ad->width) }}">
                            </div>
                            <div class="col-md-6">
                                <label for="height" class="form-label">Altezza (px)</label>
                                <input type="number" id="height" name="height" class="form-control"
                                    value="{{ old('height', $ad->height) }}">
                            </div>
                        </div>

                        {{-- DATES --}}
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="start_date" class="form-label">Data inizio</label>
                                <input type="date" id="start_date" name="start_date" class="form-control"
                                    value="{{ old('start_date', $ad->start_date ?? date('Y-m-d')) }}">
                            </div>
                            <div class="col-md-6">
                                <label for="expiry_date" class="form-label">Data scadenza</label>
                                <input type="date" id="expiry_date" name="expiry_date" class="form-control"
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
                    </div>
                </div>
            </div>

            {{-- SIDEBAR (leave empty or add cards like in create page) --}}
        </div>

        {{-- hidden field collects ids of existing images to delete --}}
        <input type="hidden" name="deleted_images" id="deleted_images" value="">
    </form>
@endsection

{{-- SCRIPTS ------------------------------------------------------------ --}}
@push('footer')
    {{-- CodeMirror for AMP (optional) --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.5/codemirror.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.5/codemirror.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.5/mode/javascript/javascript.min.js"></script>

    <script>
        (function() {
            /* ---------- DOM refs ---------- */
            const typeSelect = document.getElementById('advanced-ad-type');
            const imgSection = document.getElementById('imageUploadSection');
            const ampSection = document.getElementById('googleAdImageNameSection');
            const fileInput = document.getElementById('imageUpload');
            const previewWrapper = document.getElementById('previewWrapper');
            const deletedField = document.getElementById('deleted_images');

            /* ---------- state ---------- */
            let newFiles = []; // File objects chosen in this session
            const deletedExisting = new Set(); // IDs of already-stored images to delete

            /* ---------- helpers ---------- */
            function syncFileInput() {
                const dt = new DataTransfer();
                newFiles.forEach(f => dt.items.add(f));
                fileInput.files = dt.files;
            }

            function renderPreviews() {
                /* clear previous new-image cards */
                previewWrapper.querySelectorAll('.new-img').forEach(n => n.remove());

                newFiles.forEach((file, idx) => {
                    const card = document.createElement('div');
                    card.className = 'new-img border rounded p-2 position-relative mt-2';

                    /* remove btn */
                    const btn = document.createElement('button');
                    btn.type = 'button';
                    btn.className = 'btn-close position-absolute top-0 end-0 m-2';
                    btn.title = 'Remove';
                    btn.onclick = () => {
                        newFiles.splice(idx, 1);
                        renderPreviews();
                        syncFileInput();
                    };
                    card.appendChild(btn);

                    /* img preview */
                    const img = document.createElement('img');
                    img.style.maxWidth = '100%';
                    img.style.height = 'auto';
                    const rdr = new FileReader();
                    rdr.onload = e => img.src = e.target.result;
                    rdr.readAsDataURL(file);
                    card.appendChild(img);

                    /* url field that travels with the file */
                    const url = document.createElement('input');
                    url.type = 'url';
                    url.name = `urls_new[${idx}]`;
                    url.placeholder = 'https://example.com';
                    url.required = true;
                    url.className = 'form-control mt-2';
                    card.appendChild(url);

                    previewWrapper.appendChild(card);
                });
            }

            /* ---------- event wires ---------- */
            /* 1) switch sections when type changes */
            typeSelect.addEventListener('change', e => {
                const isGoogleAd = parseInt(e.target.value) === 2;
                imgSection.style.display = isGoogleAd ? 'none' : 'block';
                ampSection.style.display = isGoogleAd ? 'block' : 'none';
            });

            /* 2) existing image remove buttons (rendered server-side) */
            document.querySelectorAll('.existing-img .remove-btn').forEach(btn => {
                btn.addEventListener('click', () => {
                    const card = btn.closest('.existing-img');
                    deletedExisting.add(card.dataset.id);
                    card.remove();
                    deletedField.value = [...deletedExisting].join(',');
                });
            });

            /* 3) new file picker */
            fileInput?.addEventListener('change', e => {
                newFiles = [...e.target.files];
                renderPreviews();
                syncFileInput();
            });

            /* 4) AMP textarea → CodeMirror */
            if (document.getElementById('amp')) {
                CodeMirror.fromTextArea(document.getElementById('amp'), {
                    lineNumbers: true,
                    mode: 'javascript',
                    theme: 'default'
                });
            }
        })();
    </script>
@endpush
