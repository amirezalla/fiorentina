@extends(BaseHelper::getAdminMasterLayoutTemplate())

@section('content')
    <div class="container-fluid">
        <h1 class="mb-4">Ads Positions</h1>

        <!-- Nav Tabs -->
        <ul class="nav nav-tabs" id="groupTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="desktop-tab" data-bs-toggle="tab" data-bs-target="#desktop" type="button"
                    role="tab" aria-controls="desktop" aria-selected="true">
                    Desktop
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="mobile-tab" data-bs-toggle="tab" data-bs-target="#mobile" type="button"
                    role="tab" aria-controls="mobile" aria-selected="false">
                    Mobile
                </button>
            </li>
        </ul>

        <div class="tab-content mt-3" id="groupTabsContent">
            <!-- Desktop Groups -->
            <div class="tab-pane fade show active" id="desktop" role="tabpanel" aria-labelledby="desktop-tab">
                <div class="table-responsive">
                    <table class="table table-striped align-middle">
                        <thead>
                            <tr>
                                <th>Group ID</th>
                                <th>Group Name</th>
                                <th>Count of Ads</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($desktopGroups as $groupId => $groupName)
                                <tr>
                                    <td>{{ $groupId }}</td>
                                    <td>{{ $groupName }}</td>
                                    <td>{{ $counts[$groupId] ?? 0 }}</td>
                                    <td>
                                        <div class="d-flex gap-2">
                                            <!-- Manage button using icon -->
                                            <a href="{{ route('ads.index', ['group' => $groupId]) }}"
                                                class="btn  btn-primary" style='padding:10px 1px 7px 11px'
                                                aria-label="Manage Group Ads">
                                                <i class="fa fa-list-check"></i>
                                            </a>
                                            <!-- Sort button using icon -->
                                            <a href="{{ route('ads.sort', ['group' => $groupId]) }}"
                                                class="btn  btn-secondary" style='padding:10px 2px 8px 14px'
                                                aria-label="Sort Weight">
                                                <i class="fa fa-sort"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4">No desktop groups found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Mobile Groups -->
            <div class="tab-pane fade" id="mobile" role="tabpanel" aria-labelledby="mobile-tab">
                <div class="table-responsive">
                    <table class="table table-striped align-middle">
                        <thead>
                            <tr>
                                <th>Group ID</th>
                                <th>Group Name</th>
                                <th>Count of Ads</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($mobileGroups as $groupId => $groupName)
                                <tr>
                                    <td>{{ $groupId }}</td>
                                    <td>{{ $groupName }}</td>
                                    <td>{{ $counts[$groupId] ?? 0 }}</td>
                                    <td>
                                        <div class="d-flex gap-2">
                                            <a href="{{ route('ads.index', ['group' => $groupId]) }}"
                                                class="btn  btn-primary" style='padding:10px 1px 7px 11px'
                                                aria-label="Manage Group Ads">
                                                <i class="fa-solid fa-list-check"></i>
                                            </a>
                                            <a href="{{ route('ads.sort', ['group' => $groupId]) }}"
                                                class="btn  btn-secondary" style='padding:10px 2px 8px 14px'
                                                aria-label="Sort Weight">
                                                <i class="fa-solid fa-sort"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4">No mobile groups found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Note Section -->
        <div class="mt-4">
            <small class="text-muted">
                Note: When an ad has multiple images, its total weight is divided evenly among the images.
                For example, if an ad has a weight of 6 and contains 3 images, the ad will appear 6 times in rotation
                overall,
                with each image displayed approximately 2 times.
            </small>
        </div>
    </div>
@endsection
