{{-- resources/views/ad_groups/edit.blade.php --}}
@extends(BaseHelper::getAdminMasterLayoutTemplate())

@section('content')
    <div class="container-fluid">
        <h1 class="mb-4">Edit Group: {{ $group->name }}</h1>

        {{-- flashes & errors --}}
        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if (session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach ($errors->all() as $e)
                        <li>{{ $e }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- ================== GROUP META ================== --}}
        <form method="POST" action="{{ route('adgroups.update', $group) }}" class="mb-4">
            @csrf @method('PUT')

            <div class="row">
                <div class="col-lg-8">

                    <div class="card mb-3">
                        <div class="card-header">
                            <h4 class="card-title mb-0">Group details</h4>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <label class="form-label">Name</label>
                                <input class="form-control" name="name" value="{{ old('name', $group->name) }}" required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Slug</label>
                                <input class="form-control" name="slug" value="{{ old('slug', $group->slug) }}" required>
                                <small class="text-muted">Unique key, e.g. <code>desktop_728x90_top</code></small>
                            </div>

                            <div class="row g-2">
                                <div class="col">
                                    <label class="form-label">Width (px)</label>
                                    <input type="number" class="form-control" name="width"
                                        value="{{ old('width', $group->width) }}">
                                </div>
                                <div class="col">
                                    <label class="form-label">Height (px)</label>
                                    <input type="number" class="form-control" name="height"
                                        value="{{ old('height', $group->height) }}">
                                </div>
                            </div>

                            <div class="mt-3">
                                <label class="form-label d-block">Placement</label>
                                <select name="placement" class="form-select">
                                    <option value="" @selected(old('placement', $group->placement) == '')>—</option>
                                    <option value="homepage" @selected(old('placement', $group->placement) == 'homepage')>Homepage</option>
                                    <option value="article" @selected(old('placement', $group->placement) == 'article')>Article</option>
                                    <option value="both" @selected(old('placement', $group->placement) == 'both')>Both</option>
                                </select>
                            </div>

                            <div class="mt-3">
                                <label class="form-label d-block">Status</label>
                                <select name="status" class="form-select">
                                    <option value="1" @selected(old('status', $group->status) == 1)>Active</option>
                                    <option value="0" @selected(old('status', $group->status) == 0)>Disabled</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex gap-2">
                        <button class="btn btn-primary">Save</button>
                        <a class="btn btn-secondary" href="{{ route('adgroups.index') }}">Back</a>
                    </div>
                </div>
            </div>
        </form>

        {{-- ================== UPLOAD IMAGES + LINKS ================== --}}
        <div class="card mb-4">
            <div class="card-header">
                <h4 class="card-title mb-0">Add images to this group
                    ({{ $group->width ?: 'auto' }}×{{ $group->height ?: 'auto' }})</h4>
            </div>
            <div class="card-body">
                <form action="{{ route('adgroups.images.store', $group) }}" method="POST" enctype="multipart/form-data"
                    id="uploadForm">
                    @csrf
                    <div class="row g-3 align-items-end">
                        <div class="col-12 col-lg-6">
                            <label class="form-label">Images</label>
                            <input type="file" class="form-control" name="images[]" id="imagesInput" multiple
                                accept="image/*">
                            <small class="text-muted d-block">
                                Images will be resized to the group size if width/height are set.
                            </small>
                        </div>
                        <div class="col-12 col-lg-6">
                            <label class="form-label">Target links (matched by order)</label>
                            <div id="urlInputs" class="d-flex flex-column gap-2"></div>
                            <small class="text-muted">Optional. If provided, each link will be attached to the corresponding
                                image.</small>
                        </div>
                        <div class="col-12">
                            <button class="btn btn-primary">Add Images</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        {{-- ================== CURRENT IMAGES GRID ================== --}}
        @php
            $resolve = fn($path) => preg_match('~^https?://~', $path) ? $path : Storage::disk('wasabi')->url($path);
        @endphp

        <div class="card">
            <div class="card-header">
                <h4 class="card-title mb-0">Current images ({{ $group->images->count() }})</h4>
            </div>
            <div class="card-body">

                <div id="images-grid" class="row g-3">
                    @forelse ($group->images as $img)
                        <div class="col-12 col-sm-6 col-md-4 col-lg-3" data-id="{{ $img->id }}">
                            <div class="border rounded p-2 h-100 position-relative">
                                <img src="{{ $resolve($img->image_url) }}" alt="" class="img-fluid w-100"
                                    style="aspect-ratio: 1.5/1; object-fit: cover;">
                                <div class="mt-2 small text-truncate">
                                    <span class="text-muted">Link: </span>
                                    @if ($img->target_url)
                                        <a href="{{ $img->target_url }}" target="_blank"
                                            rel="noopener">{{ $img->target_url }}</a>
                                    @else
                                        <em class="text-muted">—</em>
                                    @endif
                                </div>
                                <form action="{{ route('adgroups.images.destroy', [$group, $img]) }}" method="POST"
                                    onsubmit="return confirm('Remove this image?');"
                                    class="position-absolute top-0 end-0 m-2">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-danger" type="submit" title="Delete">&times;</button>
                                </form>
                            </div>
                        </div>
                    @empty
                        <div class="col-12"><em class="text-muted">No images yet.</em></div>
                    @endforelse
                </div>

                {{-- ================== EDIT LINKS INLINE ================== --}}
                @if ($group->images->count())
                    <hr class="my-4">
                    <h5>Update links for existing images</h5>

                    <form action="{{ route('adgroups.images.update-links', $group) }}" method="POST">
                        @csrf
                        <div class="row g-3">
                            @foreach ($group->images as $img)
                                <div class="col-12 col-md-6">
                                    <div class="border rounded p-2 h-100">
                                        <div class="d-flex gap-2 align-items-start">
                                            <img src="{{ $resolve($img->image_url) }}" alt=""
                                                style="width:120px;height:120px;object-fit:cover;border-radius:.25rem;">
                                            <div class="flex-grow-1">
                                                <label class="form-label">Target URL</label>
                                                <input type="url" class="form-control"
                                                    name="image_urls[{{ $img->id }}]"
                                                    value="{{ old('image_urls.' . $img->id, $img->target_url) }}"
                                                    placeholder="https://example.com">
                                                <small class="text-muted">Image #{{ $img->id }}</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <div class="mt-3">
                            <button class="btn btn-primary">Save Links</button>
                        </div>
                    </form>
                @endif
            </div>
        </div>
    </div>
@endsection

@push('footer')
    <script>
        (function() {
            const fileInput = document.getElementById('imagesInput');
            const urlInputs = document.getElementById('urlInputs');

            fileInput?.addEventListener('change', () => {
                urlInputs.innerHTML = '';
                const files = [...fileInput.files];

                files.forEach((f, i) => {
                    const group = document.createElement('div');
                    group.className = 'input-group';

                    const pref = document.createElement('span');
                    pref.className = 'input-group-text';
                    pref.textContent = (i + 1) + '.';
                    group.appendChild(pref);

                    const input = document.createElement('input');
                    input.type = 'url';
                    input.name = 'urls[' + i + ']';
                    input.placeholder = 'https://example.com (optional)';
                    input.className = 'form-control';
                    group.appendChild(input);

                    urlInputs.appendChild(group);
                });
            });
        })();
    </script>
@endpush
