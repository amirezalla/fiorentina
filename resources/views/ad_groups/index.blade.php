@extends(BaseHelper::getAdminMasterLayoutTemplate())

@section('content')
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h1 class="mb-0">Ad Groups</h1>
            <a class="btn btn-primary" href="{{ route('adgroups.create') }}">Create Group</a>
        </div>

        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if (session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        <div class="table-responsive">
            <table class="table table-striped align-middle">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Slug</th>
                        <th>Size</th>
                        <th>Placement</th>
                        <th>Status</th>
                        <th>Images</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($groups as $g)
                        <tr>
                            <td>{{ $g->name }}</td>
                            <td><code>{{ $g->slug }}</code></td>
                            <td>{{ $g->width }}×{{ $g->height }}</td>
                            <td>{{ $g->placement ?: '—' }}</td>
                            <td>
                                @if ($g->status)
                                    <span class="badge bg-success">Active</span>
                                @else
                                    <span class="badge bg-secondary">Disabled</span>
                                @endif
                            </td>
                            <td>{{ $g->images_count }}</td>
                            <td class="d-flex gap-2">
                                <a href="{{ route('adgroups.edit', $g) }}" class="btn btn-primary btn-sm">Edit</a>
                                <form action="{{ route('adgroups.destroy', $g) }}" method="POST"
                                    onsubmit="return confirm('Delete this group (and its images)?');">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-danger btn-sm" type="submit">Delete</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7">No groups yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-3">
            {{ $groups->links() }}
        </div>
    </div>
@endsection
