@extends(BaseHelper::getAdminMasterLayoutTemplate())

@section('content')
    <form action="{{ route('videos.update', $video->id) }}" method="POST" enctype="multipart/form-data">
        @csrf <!-- CSRF Token for Laravel, ensures your form is secure -->
        @method('PUT')

        <div class="row">
            <div class="gap-3 col-md-9">
                <div class="card mb-3">
                    <div class="card-body">
                        <!-- Title Input -->
                        <div class="mb-3">
                            <label for="title" class="form-label">Advertisement Title</label>
                            <input type="text" class="form-control" name="title" id="title" value="{{ $video->title }}"
                                   required>
                        </div>

                        <!-- Video Upload Input (multiple) -->
                        <div class="mb-3">
                            <label for="videoUpload" class="form-label">Upload Videos</label>
                            <span data-bb-toggle="video-picker-choose"
                                  data-target="popup" class="btn btn-primary">
                                Choose videos
                            </span>
                            @error('videos')
                            <span class="is-invalid text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Video Previews -->
                        <div class="mb-3">
                            <label for="videoPreview" class="form-label">Video Previews</label>
                            <div id="videoPreviewContainer" class="row">
                                @if($video->mediaFiles->count())
                                    @foreach($video->mediaFiles as $mediaFile)
                                        <div class="col-12 col-md-6 col-lg-4 mb-3 video-preview-item">
                                            <input type="hidden" name="videos[{{ $mediaFile->id }}][media_id]" value="{{ $mediaFile->id }}">
                                            <div class="w-100 p-2 border border-2 rounded-2">
                                                <video src="{{ $mediaFile->previewUrl }}" class="w-100" controls></video>
                                                <div class="mt-1">
                                                    <label for="order-video-select-{{ $mediaFile->id }}" class="form-label">Order</label>
                                                    <select name="videos[{{ $mediaFile->id }}][order]" class="form-control order-video-select" id="order-video-select-{{ $mediaFile->id }}">
                                                        <option>DEFAULT</option>
                                                        @foreach($video->mediaFiles as $key => $mf)
                                                            <option value="{{ $key++ }}" @if($mediaFile->pivot->priority == $key++) selected @endif>{{ $key++ }}</option>
                                                        @endforeach
                                                    </select>
                                                    <label for="link-video-input-{{ $mediaFile->id }}" class="form-label">Url</label>
                                                    <input type="text" class="form-control mb-2" id="link-video-input-{{ $mediaFile->id }}" name="videos[{{ $mediaFile->id }}][url]" value="{{ $mediaFile->pivot->url }}">
                                                    <button type="button" class="btn btn-danger video-preview-item-delete">
                                                        Delete
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                @endif
                            </div>
                        </div>

                        <!-- Delay Input (for Sequential Mode) -->
                        <div class="mb-3">
                            <label for="delay" class="form-label">Delay Between Videos (in seconds)</label>
                            <input type="number" class="form-control" name="delay" id="delay" value="{{ $video->delay / 1000 }}" min="1" step="1">
                        </div>

                        <!-- Video Mode Selection -->
                        <div class="mb-3">
                            <label for="mode" class="form-label">Playlist Mode</label>
                            <select class="form-control" id="mode" name="mode" required>
                                @foreach(\App\Models\Video::PLAYLIST_MODES as $mode)
                                    <option value="{{ $mode }}"
                                            @if($video->checkModelSelect($mode)) selected @endif>{{ $mode }}</option>
                                @endforeach
                            </select>
                            @error('mode')
                            <span class="is-invalid text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="mt-3">
                            <label class="form-check form-switch">
                                <input class="form-check-input" name="is_for_home" type="checkbox" value="1"
                                       id="is_for_home" @if($video->is_for_home) checked @endif>
                                <span class="form-check-label">Advertisement for Home</span>
                            </label>
                        </div>
                        <div class="mt-3">
                            <label class="form-check form-switch">
                                <input class="form-check-input" name="is_for_post" type="checkbox" value="1"
                                       id="is_for_post" @if($video->is_for_post) checked @endif>
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
                                <option value="{{ $status }}"
                                        @if($video->checkStatusSelect($status)) selected @endif>{{ $status }}</option>
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
        const container = $('#videoPreviewContainer');
        $.each($(document).find('[data-bb-toggle="video-picker-choose"][data-target="popup"]'), (function (e, t) {
            $(t).rvMedia({
                multiple: true,
                filter: "video",
                onSelectFiles: function (e, t) {
                    e.forEach((i, k) => {
                        const html = `
                        <div class="col-12 col-md-6 col-lg-4 mb-3 video-preview-item">
                            <input type="hidden" name="videos[${i.id}][media_id]" value="${i.id}">
                            <div class="w-100 p-2 border border-2 rounded-2">
                                <video src="${i.preview_url}" class="w-100" controls></video>
                                <div class="mt-1">
                                    <label for="order-video-select-${i.id}" class="form-label">Order</label>
                                    <select name="videos[${i.id}][order]" class="form-control order-video-select" id="order-video-select-${i.id}"></select>
                                    <label for="link-video-input-${i.id}" class="form-label">Url</label>
                                    <input type="text" class="form-control mb-2" id="link-video-input-${i.id}" name="videos[${i.id}][url]">
                                    <button type="button" class="btn btn-danger video-preview-item-delete">
                                        Delete
                                    </button>
                                </div>
                            </div>
                        </div>
                        `;
                        container.append(html);
                    });
                    updateAllOrderSelects();
                }
            })
        }));

        container.on('change', '.order-video-select', function (event) {
            const value = Number($(event.target).val());
            const id = Number($(event.target).closest('.video-preview-item').find('input[type="hidden"]').val());
            updateAllOrderSelects(value, id);
        });

        function updateAllOrderSelects(selectedValue = null, selectedId = null) {
            const videoPreviewItems = container.find('.video-preview-item');
            videoPreviewItems.each(function (key, el) {
                const element = $(el);
                const select = element.find('.order-video-select');
                let value = Number(select.val());
                const id = Number($(el).find('input[type="hidden"]').val());
                if (selectedValue && value === selectedValue && id !== selectedId) {
                    value = null;
                }
                select.empty();
                select.append(`<option ${isNaN(value) ? 'selected' : ''}>DEFAULT</option>`);
                for (let i = 1; i <= videoPreviewItems.length; i++) {
                    select.append(`<option value="${i}" ${Number(value) === i ? 'selected' : ''}>${i}</option>`);
                }
            });
        }

        $(document).on('click', '.video-preview-item-delete', function (e) {
            e.preventDefault();
            $(e.target).closest('.video-preview-item').remove();
        })

        // Toggle delay input based on Playlist Mode (show delay only for Sequential mode)
        function toggleDelayInput() {
            const mode = document.getElementById('mode').value;
            const delayInput = document.getElementById('delay').closest('.mb-3');
            if (mode === 'sequential') {
                delayInput.style.display = 'block';
            } else {
                delayInput.style.display = 'none';
            }
        }

        // Initial call to set delay visibility and add event listener
        toggleDelayInput();
        document.getElementById('mode').addEventListener('change', toggleDelayInput);
    </script>
@endpush
