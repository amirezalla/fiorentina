@extends(BaseHelper::getAdminMasterLayoutTemplate())

@section('content')
    <!-- Include CodeMirror CSS/JS if needed -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.5/codemirror.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.5/codemirror.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.5/mode/javascript/javascript.min.js"></script>

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
                                <input type="file" class="form-control mb-1" id="imageUpload" name="images[]" multiple
                                    accept="image/*">

                                <!-- container that will receive a matching URL field
                                                 for every selected image -->
                                <div id="urlFields" class="mt-2"></div>

                                <div class="row mx-0 mt-3">
                                    <div class="col-12 mt-3" id="previewContainer"></div>
                                </div>
                            </div>

                            <!-- Image Name Section for Google Ad Manager -->
                            <div class="row mb-3 mt-3" id="googleAdImageNameSection" style="display: none;">
                                <div class="form-group">
                                    <label for="code">Amp</label>
                                    <textarea id="code" name="amp" class="form-control" rows="10" placeholder="Enter your amp code here">{{ old('amp') }}</textarea>
                                </div>
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
                </div>

            </div>

        </div>
    </form>
@endsection

@push('footer')
    <script>
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

        // Add change event listener to the file input for multiple image previews.
        const fileInput = document.getElementById('imageUpload');
        const preview = document.getElementById('previewContainer');
        const urlFieldWrapper = document.getElementById('urlFields');

        fileInput.addEventListener('change', e => {
            preview.innerHTML = '';
            urlFieldWrapper.innerHTML = '';

            [...e.target.files].forEach((file, idx) => {
                // ---------- preview ----------
                const reader = new FileReader();
                reader.onload = evt => {
                    const img = document.createElement('img');
                    img.src = evt.target.result;
                    img.classList.add('img-fluid', 'mb-2', 'me-2');
                    img.style.maxWidth = '120px';
                    preview.appendChild(img);
                };
                reader.readAsDataURL(file);

                // ---------- matching url field ----------
                const div = document.createElement('div');
                div.className = 'input-group mb-2';

                div.innerHTML = `
                <span class="input-group-text">URL ${idx+1}</span>
                <input type="url"
                       class="form-control"
                       name="urls[]"
                       placeholder="https://example.com"
                       required>
            `;
                urlFieldWrapper.appendChild(div);
            });
        });

        // Initialize CodeMirror on the "code" textarea if it exists.
        if (document.getElementById("code")) {
            var editor = CodeMirror.fromTextArea(document.getElementById("code"), {
                lineNumbers: true,
                mode: "javascript",
                theme: "default"
            });
        }
    </script>
@endpush
