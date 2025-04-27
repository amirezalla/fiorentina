@extends(BaseHelper::getAdminMasterLayoutTemplate())

@section('content')
    <form action="{{ route('players.update', $player->id) }}" method="POST" enctype="multipart/form-data">
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
                                    <label for="name" class="form-label">Player Name</label>
                                    <input type="text" class="form-control" name="name" id="name"
                                        value="{{ old('name', $player->name) }}" spellcheck="true" autocomplete="off">
                                </div>
                            </div>

                            <!-- League Selection -->
                            <div class="mb-3">
                                <label for="league" class="form-label">League</label>
                                <input type="text" class="form-control" name="league" id="league"
                                    value="{{ old('league', $player->league) }}" required>
                            </div>

                            <!-- Position Selection -->
                            <div class="mb-3">
                                <label for="position" class="form-label">Position</label>
                                <input type="text" class="form-control" name="position" id="position"
                                    value="{{ old('position', $player->position) }}" required>
                            </div>

                            <!-- Season Selection -->
                            <div class="mb-3">
                                <label for="season" class="form-label">Season</label>
                                <select class="form-select" name="season" id="season" required>
                                    <option value="2024-2025"
                                        {{ old('season', $player->season) == '2024-2025' ? 'selected' : '' }}>2024-2025
                                    </option>
                                    <option value="2025-2026"
                                        {{ old('season', $player->season) == '2025-2026' ? 'selected' : '' }}>2025-2026
                                    </option>
                                    <option value="2026-2027"
                                        {{ old('season', $player->season) == '2026-2027' ? 'selected' : '' }}>2026-2027
                                    </option>
                                </select>
                            </div>

                            <!-- Image Upload Section -->
                            <div class="row mb-3">
                                <label for="imageUpload" class="form-label">Upload an image</label>
                                <input type="file" class="form-control" id="imageUpload" name="image" accept="image/*">
                                <div class="row mx-0 mt-3">
                                    <div class="col-12">

                                        <img src="{{ Str::startsWith($player->image, 'https://')
                                            ? $player->getImageUrl($player->name) // absolute URL already stored
                                            : $player->wasabiImage($player->name); // build Wasabi link otherwise }}"
                                            width="50" height="50" alt="{{ $player->name }}" />
                                        {{--                                        @if ($player->getImageUrl()) --}}
                                        {{--                                            <img src="{{ $player->getImageUrl() }}" class="image-preview" alt="{{ $player->name }}" style="max-width: 200px;"> --}}
                                        {{--                                        @endif --}}
                                    </div>
                                </div>
                            </div>

                            <!-- Flag ID -->
                            <div class="mb-3">
                                <label for="flag_id" class="form-label">Flag ID</label>
                                <input type="number" class="form-control" name="flag_id" id="flag_id"
                                    value="{{ old('flag_id', $player->flag_id) }}" required>
                            </div>

                            <!-- Jersey Number -->
                            <div class="mb-3">
                                <label for="jersey_number" class="form-label">Jersey Number</label>
                                <input type="number" class="form-control" name="jersey_number" id="jersey_number"
                                    value="{{ old('jersey_number', $player->jersey_number) }}" required>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-3 gap-3 d-flex flex-column-reverse flex-md-column mb-md-0 mb-5">
                <!-- Publish Section -->
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Publish</h4>
                    </div>
                    <div class="card-body">
                        <div class="btn-list">
                            <button class="btn btn-primary" type="submit" value="apply" name="submitter">
                                Save
                            </button>

                            <button class="btn" type="submit" name="submitter" value="save">
                                Save & Exit
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Status Section -->
                <div class="card meta-boxes">
                    <div class="card-header">
                        <h4 class="card-title">
                            <label for="status" class="form-label required">Status</label>
                        </h4>
                    </div>
                    <div class="card-body">
                        <select data-placeholder="Select an option" class="form-control form-select" required="required"
                            id="status" name="status" aria-required="true">
                            <option value="published"
                                {{ old('status', $player->status) == 'published' ? 'selected' : '' }}>Published</option>
                            <option value="draft" {{ old('status', $player->status) == 'draft' ? 'selected' : '' }}>Draft
                            </option>
                            <option value="pending" {{ old('status', $player->status) == 'pending' ? 'selected' : '' }}>
                                Pending</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>
    </form>
@endsection

@push('footer')
    <script>
        document.getElementById('imageUpload').addEventListener('change', function(e) {
            const input = e.target;
            if (input.files && input.files[0]) {
                var reader = new FileReader();
                reader.onload = function(e) {
                    document.querySelector('.image-preview').setAttribute('src', e.target.result);
                    document.querySelector('.image-preview').style.display = 'block';
                }
                reader.readAsDataURL(input.files[0]);
            }
        });
    </script>
@endpush
