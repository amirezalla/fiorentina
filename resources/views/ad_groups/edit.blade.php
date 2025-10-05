@extends(BaseHelper::getAdminMasterLayoutTemplate())

@section('content')
    <div class="container-fluid">
        <h1 class="mb-4">Edit Group: {{ $group->name }}</h1>

        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
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

        <form method="POST" action="{{ route('adgroups.update', $group) }}" class="mb-4">
            @csrf @method('PUT')

            <div class="row">
                <div class="col-md-8">

                    <div class="card mb-3">
                        <div class="card-body">
                            <div class="mb-3">
                                <label class="form-label">Name</label>
                                <input class="form-control" name="name" value="{{ old('name', $group->name) }}" required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Slug</label>
                                <input class="form-control" name="slug" value="{{ old('slug', $group->slug) }}" required>
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

                    <button class="btn btn-primary">Save</button>
                    <a class="btn btn-secondary" href="{{ route('adgroups.index') }}">Back</a>
                </div>
            </div>
        </form>

        {{-- IMAGES --}}
        <div class="card">
            <div class="card-header">
                <h4 class="card-title mb-0">Images ({{ $group->width }}×{{ $group->height }})</h4>
            </div>
            <div class="card-body">
                <form action="{{ route('adgroups.images.store', $group) }}" method="POST" enctype="multipart/form-data"
                    class="mb-3">
                    @csrf
                    <div class="row g-2 align-items-end">
                        <div class="col-12 col-md-6">
                            <label class="form-label">Upload images</label>
                            <input type="file" class="form-control" name="images[]" multiple accept="image/*">
                        </div>
                        <div class="col-12 col-md-3">
                            <button class="btn btn-primary">Add Images</button>
                        </div>
                    </div>
                    <small class="text-muted d-block mt-2">
                        Uploaded images will be auto-resized to this group’s size if width/height are set.
                    </small>
                </form>

                @php
                    $resolve = fn($path) => preg_match('~^https?://~', $path)
                        ? $path
                        : Storage::disk('wasabi')->url($path);
                @endphp

                <div id="images-grid" class="row g-3">
                    @forelse($group->images as $img)
                        <div class="col-6 col-sm-4 col-md-3" data-id="{{ $img->id }}">
                            <div class="border rounded p-2 h-100 position-relative">
                                <img src="{{ $resolve($img->image_url) }}" alt="" class="img-fluid">
                                <form action="{{ route('adgroups.images.destroy', [$group, $img]) }}" method="POST"
                                    onsubmit="return confirm('Remove this image?');"
                                    class="position-absolute top-0 end-0 m-2">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-danger" type="submit">&times;</button>
                                </form>
                            </div>
                        </div>
                    @empty
                        <div class="col-12"><em class="text-muted">No images yet.</em></div>
                    @endforelse
                </div>

                {{-- Optional: Drag to sort (requires position column if you want persistence) --}}
                {{-- <button id="save-order" class="btn btn-secondary mt-3">Save Order</button> --}}
            </div>
        </div>
    </div>
@endsection

@push('footer')
    <script>
        // Example client-side sort sender (uncomment Save Order button and route if you add `position` column)
        document.getElementById('save-order')?.addEventListener('click', async function() {
            const ids = Array.from(document.querySelectorAll('#images-grid [data-id]')).map(el => el.dataset
            .id);
            const resp = await fetch('{{ route('adgroups.images.sort', $group) }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    order: ids
                })
            });
            const json = await resp.json();
            if (json.ok) alert('Order saved');
        });
    </script>
@endpush
