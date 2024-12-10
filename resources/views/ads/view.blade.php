@extends(BaseHelper::getAdminMasterLayoutTemplate())
@section('content')

    <div class="w-100">
        <div class="mb-3">
            <a href="{{ route('ads.create') }}" class="btn btn-primary">Crea</a>
        </div>
        <form action="" method="get">
            <div class="row">
                <div class="col-12 col-md-6">
                    <label for="advads-group-id" class="form-label">Gruppo annunci</label>
                    <select class="form-select" name="group" id="advads-group-id">
                        @foreach (\App\Models\Ad::GROUPS as $key => $title)
                            <option value="{{ $key }}" @selected(request('group') == $key)>
                                {{ $title }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </form>
        <table class="table table-striped">
            <thead>
            <tr>
                <th>ID</th>
                <th>Title</th>
                <th>type</th>
                <th>group</th>
                <th>image</th>
                <th>Weight</th>
                <th>Status</th>

                <th>Actions</th>
            </tr>
            </thead>
            <tbody>
            @foreach ($ads as $ad)
                <tr>
                    <td class="align-middle">{{ $ad->id }}</td>
                    <td class="align-middle">{{ $ad->title }}</td>
                    <td class="align-middle">
                        @if ($ad->type == 1)
                            IMAGE/URL
                        @else
                            GOOGLE ADS
                        @endif
                    </td>
                    <td class="align-middle">{{ $ad->group_name }}</td>
                    <td class="align-middle">
                        @if ($ad->getImageUrl())
                            <img src="{{ $ad->getImageUrl() }}" width="140" alt="{{ $ad->title }}">
                        @endif
                    </td>
                    <td>{{ $ad->getWeightPercentage() }}%</td>
                    <td>
                        @if ($ad->status)
                            Published
                        @else
                            Draft
                        @endif
                    </td>


                    <td class="align-middle">
                        <div class="d-flex gap-2">
                            <a href="{{ route('ads.edit', $ad->id) }}" class="btn btn-primary">Edit</a>
                            <form action="{{ route('ads.destroy', $ad->id) }}" method="post">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger">Delete</button>
                            </form>
                        </div>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
        <div class="w-100">
            {{ $ads->links() }}
        </div>
    </div>

@stop
