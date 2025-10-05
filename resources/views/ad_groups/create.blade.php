@extends(BaseHelper::getAdminMasterLayoutTemplate())

@section('content')
    <div class="container-fluid">
        <h1 class="mb-4">Create Ad Group</h1>

        @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach ($errors->all() as $e)
                        <li>{{ $e }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('adgroups.store') }}">
            @csrf

            <div class="row">
                <div class="col-md-8">
                    <div class="card mb-3">
                        <div class="card-body">
                            <div class="mb-3">
                                <label class="form-label">Name</label>
                                <input class="form-control" name="name" value="{{ old('name') }}" required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Slug</label>
                                <input class="form-control" name="slug" value="{{ old('slug') }}" required>
                                <small class="text-muted">Unique key, e.g. <code>desktop_728x90_top</code></small>
                            </div>

                            <div class="row g-2">
                                <div class="col">
                                    <label class="form-label">Width (px)</label>
                                    <input type="number" class="form-control" name="width" value="{{ old('width') }}">
                                </div>
                                <div class="col">
                                    <label class="form-label">Height (px)</label>
                                    <input type="number" class="form-control" name="height" value="{{ old('height') }}">
                                </div>
                            </div>

                            <div class="mt-3">
                                <label class="form-label d-block">Placement</label>
                                <select name="placement" class="form-select">
                                    <option value="">—</option>
                                    <option value="homepage" @selected(old('placement') == 'homepage')>Homepage</option>
                                    <option value="article" @selected(old('placement') == 'article')>Article</option>
                                    <option value="both" @selected(old('placement') == 'both')>Both</option>
                                </select>
                            </div>

                            <div class="mt-3">
                                <label class="form-label d-block">Status</label>
                                <select name="status" class="form-select">
                                    <option value="1" @selected(old('status', 1) == 1)>Active</option>
                                    <option value="0" @selected(old('status', 1) == 0)>Disabled</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <button class="btn btn-primary">Create</button>
                    <a class="btn btn-secondary" href="{{ route('adgroups.index') }}">Back</a>
                </div>
            </div>
        </form>
    </div>
@endsection
