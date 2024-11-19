@extends(BaseHelper::getAdminMasterLayoutTemplate())

@section('content')
    <form action="{{ route('videos.store') }}" method="POST" enctype="multipart/form-data">
    @csrf <!-- CSRF Token for Laravel, ensures your form is secure -->

        <div class="row">
            <div class="gap-3 col-md-9">
                <div class="card mb-3">
                    <div class="card-body">
                        <!-- Title Input -->
                        <div class="mb-3">
                            <label for="title" class="form-label">Advertisement Title</label>
                            <input type="text" class="form-control" name="title" id="title" value="{{ old('title') }}"
                                   required>
                        </div>

                        <!-- Video Upload Input (multiple) -->
                        <div class="mb-3">
                            <label for="videoUpload" class="form-label">Upload Videos</label>
                            <span data-bb-toggle="video-picker-choose"
                                  data-target="popup" class="btn btn-primary">
                                Choice videos
                            </span>
                            @error('videos')
                            <span class="is-invalid text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Video Previews -->
                        <div class="mb-3">
                            <label for="videoPreview" class="form-label">Video Previews</label>
                            <div id="videoPreviewContainer" class="row">
                                <!-- Video previews will be injected here -->
                            </div>
                        </div>

                        <!-- Delay Selection Section -->
                        <div class=" mb-3">
                            <label for="delaySelect" class="form-label">Select Delay Between Plays (ms):</label>
                            <select class="form-select" id="delay" name="delay">
                                <option value="1">1s</option>
                                <option value="5">5s</option>
                                <option value="10">10s</option>
                                <option value="15">15s</option>
                                <option value="30">30s</option>
                                <option value="60">1min</option>
                                <option value="120">2min</option>
                            </select>

                        </div>

                        <!-- Video Mode Selection -->
                        <div class="mb-3">
                            <label for="mode" class="form-label">Playlist Mode</label>
                            <select class="form-control" id="mode" name="mode" required>
                                @foreach(\App\Models\Video::PLAYLIST_MODES as $mode)
                                    <option value="{{ $mode }}">{{ $mode }}</option>
                                @endforeach
                            </select>
                            @error('mode')
                            <span class="is-invalid text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="mt-3">
                            <label class="form-check form-switch">
                                <input class="form-check-input" name="is_for_home" type="checkbox" value="1"
                                       id="is_for_home" @if(old('is_for_home')) checked @endif>
                                <span class="form-check-label">Advertisement for Home</span>
                            </label>
                        </div>
                        <div class="mt-3">
                            <label class="form-check form-switch">
                                <input class="form-check-input" name="is_for_post" type="checkbox" value="1"
                                       id="is_for_post" @if(old('is_for_post')) checked @endif>
                                <span class="form-check-label">Advertisement for Post</span>
                            </label>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-3 gap-3 d-flex flex-column-reverse flex-md-column mb-md-0 mb-5">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Publish</h4>
                    </div>
                    <div class="card-body">
                        <div class="btn-list">
                            <button class="btn btn-primary" type="submit" value="apply" name="submitter">
                                <svg class="icon icon-left" xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                     viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                     stroke-linecap="round" stroke-linejoin="round">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                    <path
                                        d="M6 4h10l4 4v10a2 2 0 0 1 -2 2h-12a2 2 0 0 1 -2 -2v-12a2 2 0 0 1 2 -2"></path>
                                    <path d="M12 14m-2 0a2 2 0 1 0 4 0a2 2 0 1 0 -4 0"></path>
                                    <path d="M14 4l0 4l-6 0l0 -4"></path>
                                </svg>
                                Save
                            </button>

                            <button class="btn" type="submit" name="submitter" value="save">
                                <svg class="icon icon-left" xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                     viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                     stroke-linecap="round" stroke-linejoin="round">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                    <path d="M4 18v3h16v-14l-8 -4l-8 4v3"></path>
                                    <path d="M4 14h9"></path>
                                    <path d="M10 11l3 3l-3 3"></path>
                                </svg>
                                Save & Exit
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

                    <div class="card-body">
                        <select class="form-control form-select" required="required" id="status" name="status"
                                aria-required="true">
                            @foreach(\App\Models\Video::STATUSES as $status)
                                <option value="{{ $status }}">{{ $status }}</option>
                            @endforeach
                        </select>
                        @error('status')
                        <span class="is-invalid text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
            </div>
        </div>
    </form>
@endsection

@push('footer')
    <script>
        // Keep track of the number of uploaded videos to set the correct options for ordering
        let totalVideos = 0;

        $.each($(document).find('[data-bb-toggle="video-picker-choose"][data-target="popup"]'), (function (e, t) {
            $(t).rvMedia({
                multiple: true,
                filter: "video",
                onSelectFiles: function (e, t) {
                    const container = $('#videoPreviewContainer');
                    const currentVideoCount = container.children().length; // Count existing videos in the container
                    totalVideos = currentVideoCount + e.length; // Update totalVideos based on newly added videos

                    e.forEach((i, index) => {
                        // Automatically assign order based on upload sequence
                        const orderValue = currentVideoCount + index + 1; // Start from the next available order
                        const html = `
                            <div class="col-12 col-md-6 col-lg-4 mb-3 video-preview-item">
                                <input type="hidden" name="videos[]" value="${i.id}">
                                <div class="w-100 p-2 border border-2 rounded-2">
                                    <video src="${i.preview_url}" class="w-100" controls></video>
                                    <div class="mt-1">
                                        <label for="orderSelect-${i.id}">Select Order</label>
                                        <select name="order[${i.id}]" id="orderSelect-${i.id}" class="form-select">
                                            ${generateOrderOptions(orderValue)}
                                        </select>
<label for="title" class="form-label">link video</label>
<input type="text" class="form-control" name="videolink" id="videolink">
                                        <button type="button" class="btn btn-danger video-preview-item-delete mt-2">
                                            Delete
                                        </button>
                                    </div>
                                </div>
                            </div>
                        `;
                        container.append(html);
                    });

                    // Update the order select options for all videos based on the new total count
                    updateAllOrderSelectBoxes(totalVideos);
                }
            });
        }));

        // Function to generate select options up to totalVideos, with the specified selected order
        function generateOrderOptions(selectedOrder) {
            let options = '';
            for (let j = 1; j <= totalVideos; j++) {
                options += `<option value="${j}" ${j === selectedOrder ? 'selected' : ''}>${j}</option>`;
            }
            return options;
        }

        // Function to update the order select options for all existing videos
        function updateAllOrderSelectBoxes(totalVideos) {
            const allSelectBoxes = document.querySelectorAll('#videoPreviewContainer select');
            allSelectBoxes.forEach((selectBox, index) => {
                let options = '';
                for (let j = 1; j <= totalVideos; j++) {
                    options += `<option value="${j}" ${j === index + 1 ? 'selected' : ''}>${j}</option>`;
                }
                selectBox.innerHTML = options;
            });
        }

        // Handle video deletion from the preview
        $(document).on('click', '.video-preview-item-delete', function (e) {
            e.preventDefault();
            $(e.target).closest('.video-preview-item').remove();
            totalVideos--; // Decrement total videos when one is deleted
            updateAllOrderSelectBoxes(totalVideos); // Update all select boxes after deletion
        });
    </script>
@endpush
