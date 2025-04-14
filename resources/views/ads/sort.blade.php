@extends(BaseHelper::getAdminMasterLayoutTemplate())

@section('content')
    <div class="container">
        <h1 class="mb-4">Sort Ads for Group: {{ $groupName }}</h1>

        @if (session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger">
                {{ session('error') }}
            </div>
        @endif

        <form action="{{ route('ads.sort.update') }}" method="POST">
            @csrf
            <!-- Pass along group id -->
            <input type="hidden" name="group" value="{{ $groupId }}">

            <div class="table-responsive">
                <table class="table table-striped align-middle">
                    <thead>
                        <tr>
                            <th>Ad ID</th>
                            <th>Title</th>
                            <th>Current Weight</th>
                            <th>New Weight</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($ads as $ad)
                            <tr>
                                <td>{{ $ad->id }}</td>
                                <td>{{ $ad->title }}</td>
                                <td>{{ $ad->weight }}</td>
                                <td>
                                    <input type="number" name="weights[{{ $ad->id }}]"
                                        value="{{ old('weights.' . $ad->id, $ad->weight) }}" class="form-control"
                                        step="0.1" min="0" required>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4">No ads found for this group.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mb-3">
                <button type="submit" class="btn btn-primary">Update Weights</button>
            </div>
        </form>

        <div class="alert alert-info mt-3">
            <small>
                Note: If an ad has multiple images, its total weight is divided evenly among those images.
                For example, if an ad has a weight of 6 and contains 3 images, the ad will be shown 6 times overall,
                with each image appearing approximately 2 times in rotation.
            </small>
        </div>
    </div>
@endsection
