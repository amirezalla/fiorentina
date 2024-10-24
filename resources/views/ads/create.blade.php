@extends(BaseHelper::getAdminMasterLayoutTemplate())

@section('content')
    <form action="{{ route('ads.store') }}" method="POST" enctype="multipart/form-data">
        @csrf <!-- CSRF Token for Laravel, ensures your form is secure -->

        <div class="row">
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

                            <!-- Publish Section -->
                            <div class="postbox-container" id="postbox-container-1">
                                <div class="meta-box-sortables ui-sortable" id="side-sortables">
                                    <div class="postbox" id="submitdiv">
                                        <div class="postbox-header">
                                            <h2 class="hndle ui-sortable-handle">Pubblica</h2>
                                        </div>
                                    </div>
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
                            <div class="row mb-3" id="imageUploadSection">
                                <label for="imageUpload" class="form-label">Upload an image:</label>
                                <input type="file" class="form-control" id="imageUpload" name="image" accept="image/*">
                                <div class="row mx-0 mt-3">
                                    <div class="col-12">
                                        <img src="" class="image-preview" alt="">
                                    </div>
                                </div>
                            </div>

                            <!-- Image Name Section for Google Ad Manager -->
                            <div class="row mb-3" id="googleAdImageNameSection" style="display: none;">
                                <label for="imageName" class="form-label">Image Name:</label>
                                <input type="text" class="form-control" id="imageName" name="image_name"
                                    placeholder="XXXXXXXXX">
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
                                            value="{{ $ad_width ?? null }}">
                                    </div>
                                    <div class="mt-3">
                                        <label for="height" class="form-label">Altezza (px)</label>
                                        <input type="number" class="form-control" id="height" name="height"
                                            value="{{ $ad_height ?? null }}">
                                    </div>
                                    <div class="form-check mt-3">
                                        <input class="form-check-input" type="checkbox" id="advads-wrapper-add-sizes"
                                            name="advanced_ad[output][add_wrapper_sizes]" value="true">
                                        <label class="form-check-label" for="advads-wrapper-add-sizes">Prenota questo
                                            spazio</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
            <div class="col-md-3 gap-3 d-flex flex-column-reverse flex-md-column mb-md-0 mb-5">
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

                <div class="card meta-boxes">
                    <div class="card-header">
                        <h4 class="card-title">
                            <label for="status" class="form-label required">Status</label>
                        </h4>
                    </div>

                    <div class=" card-body">
                        <select class="form-control form-select" required="required" id="status" name="status"
                            aria-required="true">
                            <option value="published">Published</option>
                            <option value="draft">Draft</option>
                            <option value="pending">Pending</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>
    </form>
@endsection

@push('footer')
    <script>
        document.getElementById('advanced-ad-type').addEventListener('change', function(e) {
            const selectedAdType = e.target.value;
            const googleAdType = 'google-ad-manager'; // Assuming the value for Google Ad Manager is this
            const imageUploadSection = document.getElementById('imageUploadSection');
            const googleAdImageNameSection = document.getElementById('googleAdImageNameSection');

            if (selectedAdType === googleAdType) {
                // Hide the image upload section and show the image name input field
                imageUploadSection.style.display = 'none';
                googleAdImageNameSection.style.display = 'block';
            } else {
                // Show the image upload section and hide the image name input field
                imageUploadSection.style.display = 'block';
                googleAdImageNameSection.style.display = 'none';
            }
        });

        document.getElementById('imageUpload').addEventListener('change', function(e) {
            const input = e.target;
            if (input.files && input.files[0]) {
                var reader = new FileReader();
                reader.onload = function(e) {
                    document.querySelector('.image-preview').src = e.target.result;
                }
                reader.readAsDataURL(input.files[0]);
            }
        });
    </script>
@endpush
