@extends(BaseHelper::getAdminMasterLayoutTemplate())

@section('content')
    <form action="{{ route('ads.update', $ad->id) }}" method="POST" enctype="multipart/form-data">
        @dd($ad->status)
        @csrf
        @method('PUT')
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
                                        value="{{ $ad->title }}" spellcheck="true" autocomplete="off">
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
                                                    <option value="{{ $key }}" @selected($ad->type == $key)>
                                                        {{ $title }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Image Upload Section -->
                            <div class="row mt-3 mb-3" id="imageUploadSection"
                                @if ($ad->type == 2) style="display: none;" @endif>
                                <input type="file" class="form-control mb-1" id="imageUpload" name="image"
                                    accept="image/*">
                                <input type="text" class="form-control" name="url" id="url"
                                    placeholder="https://example.com" value="{{ $ad->url }}">
                                <div class="row mx-0 mt-3">
                                    <div class="col-12">
                                        <img src="{{ $ad->getImageUrl() }}" class="image-preview" alt="{{ $ad->title }}">
                                    </div>
                                </div>
                            </div>

                            <!-- Image Name Section for Google Ad Manager -->

                            <div class="row mb-3 mt-3" id="googleAdImageNameSection"
                                @if ($ad->type == 1) style="display: none;" @endif>
                                <div class="form-group">
                                    <label for="code">Amp</label>
                                    <textarea id="code" name="amp" class="form-control" rows="10" placeholder="Enter your amp code here">{{ $ad->amp }}</textarea>
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
                                            <option value="{{ $key }}" @selected($ad->group == $key)>
                                                {{ $title }}</option>
                                        @endforeach
                                    </select>

                                    <div class="mt-3">
                                        <label for="width" class="form-label">Larghezza (px)</label>
                                        <input type="number" class="form-control" id="width" name="width"
                                            value="{{ $ad->width }}">
                                    </div>
                                    <div class="mt-3">
                                        <label for="height" class="form-label">Altezza (px)</label>
                                        <input type="number" class="form-control" id="height" name="height"
                                            value="{{ $ad->height }}">
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
                                <svg class="icon icon-left" xmlns="http://www.w3.org/2000/svg" width="24"
                                    height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                    stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                    <path d="M6 4h10l4 4v10a2 2 0 0 1 -2 2h-12a2 2 0 0 1 -2 -2v-12a2 2 0 0 1 2 -2"></path>
                                    <path d="M12 14m-2 0a2 2 0 1 0 4 0a2 2 0 1 0 -4 0"></path>
                                    <path d="M14 4l0 4l-6 0l0 -4"></path>
                                </svg>
                                Save

                            </button>

                            <button class="btn" type="submit" name="submitter" value="save">
                                <svg class="icon icon-left" xmlns="http://www.w3.org/2000/svg" width="24"
                                    height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                    stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                    <path d="M4 18v3h16v-14l-8 -4l-8 4v3"></path>
                                    <path d="M4 14h9"></path>
                                    <path d="M10 11l3 3l-3 3"></path>
                                </svg>
                                Save &amp; Exit

                            </button>


                        </div>
                    </div>
                </div>

                <div data-bb-waypoint="" data-bb-target="#form-actions"></div>

                <header class="top-0 w-100 position-fixed end-0 z-1000 vertical-wrapper" id="form-actions"
                    style="display: none;">
                    <div class="navbar">
                        <div class="container-xl">
                            <div class="row g-2 align-items-center w-100">
                                <div class="col">
                                    <div class="page-pretitle">
                                        <nav aria-label="breadcrumb">
                                            <ol class="breadcrumb">
                                            </ol>
                                        </nav>

                                    </div>
                                </div>
                                <div class="col-auto ms-auto d-print-none">
                                    <div class="btn-list">
                                        <button class="btn btn-primary" type="submit" value="apply" name="submitter">
                                            <svg class="icon icon-left" xmlns="http://www.w3.org/2000/svg" width="24"
                                                height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                                <path
                                                    d="M6 4h10l4 4v10a2 2 0 0 1 -2 2h-12a2 2 0 0 1 -2 -2v-12a2 2 0 0 1 2 -2">
                                                </path>
                                                <path d="M12 14m-2 0a2 2 0 1 0 4 0a2 2 0 1 0 -4 0"></path>
                                                <path d="M14 4l0 4l-6 0l0 -4"></path>
                                            </svg>
                                            Save

                                        </button>

                                        <button class="btn" type="submit" name="submitter" value="save">
                                            <svg class="icon icon-left" xmlns="http://www.w3.org/2000/svg" width="24"
                                                height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                                <path d="M4 18v3h16v-14l-8 -4l-8 4v3"></path>
                                                <path d="M4 14h9"></path>
                                                <path d="M10 11l3 3l-3 3"></path>
                                            </svg>
                                            Save &amp; Exit

                                        </button>


                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </header>
                <div class="card meta-boxes">
                    <div class="card-header">
                        <h4 class="card-title">
                            <label for="status" class="form-label required">Status</label>
                        </h4>
                    </div>
                    <div class=" card-body">
                        <select data-placeholder="Select an option" class="form-control form-select" required="required"
                            id="status" name="status" aria-required="true">
                            <option @if ($ad->status == 1) selected @endif value="1">Published</option>
                            <option @if ($ad->status == 0) selected @endif value="0">Draft</option>
                            <option @if ($ad->status == 2) selected @endif value="2">Pending</option>
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

            // Log the selected ad type to ensure you're capturing the correct value
            console.log('Selected Ad Type:', selectedAdType);

            const googleAdType = 2; // Assuming the correct value here
            const imageUploadSection = document.getElementById('imageUploadSection');
            const googleAdImageNameSection = document.getElementById('googleAdImageNameSection');

            // Check if the value matches
            if (selectedAdType == googleAdType) {
                console.log('Google Ad Manager selected. Hiding image upload and showing image name field.');
                // Hide the image upload section and show the image name input field
                imageUploadSection.style.display = 'none';
                googleAdImageNameSection.style.display = 'block';
            } else {
                console.log('Other Ad Type selected. Showing image upload and hiding image name field.');
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
                    $('.image-preview').attr('src', e.target.result);
                }
                reader.readAsDataURL(input.files[0]);
            }
        })
    </script>
@endpush
