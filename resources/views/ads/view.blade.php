{{-- resources/views/ads/index.blade.php --}}
@extends(BaseHelper::getAdminMasterLayoutTemplate())

@section('content')

    <div class="w-100">
        <div class="mb-3">
            <a href="{{ route('ads.create') }}" class="btn btn-primary">Crea</a>
        </div>

        <form action="" method="get" class="mb-3">
            <div class="row">
                <div class="col-12 col-md-2 mb-2">
                    <label for="search-group" class="form-label">Gruppo annunci</label>
                    <select class="form-select" name="group" id="search-group">
                        <option value="">All</option>
                        @foreach (\App\Models\Ad::GROUPS as $key => $title)
                            <option value="{{ $key }}" @selected(request('group') == $key)>
                                {{ $title }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-12 col-md-2 mb-2">
                    <label for="search-status" class="form-label">Status</label>
                    <select class="form-select" name="status" id="search-status">
                        <option value="">All</option>
                        <option value="1" @if (request()->filled('status') && request('status') == 1) selected @endif>Published</option>
                        <option value="2" @if (request()->filled('status') && request('status') == 2) selected @endif>Draft</option>
                    </select>
                </div>
                <div class="col-12 col-md-2 mb-2">
                    <label for="search-q" class="form-label">Title</label>
                    <input type="text" class="form-control" id="search-q" name="q" value="{{ request('q') }}">
                </div>
                <div class="col-12 col-md-2 mb-2">
                    <label for="search-tf" class="form-label">Statistics timeframe</label>
                    <select class="form-select" name="tf" id="search-tf">
                        <option value="all" @selected($tf == 'all')>All time</option>
                        <option value="today"@selected($tf == 'today')>Today</option>
                        <option value="7" @selected($tf == '7')>Last 7 days</option>
                        <option value="30" @selected($tf == '30')>Last 30 days</option>
                        <option value="90" @selected($tf == '90')>Last 90 days</option>
                    </select>
                </div>
                <div class="col-12 col-md-3 mb-2" style="align-content: end">
                    <button type="submit" class="col-12 btn btn-primary">Search</button>
                </div>
            </div>
        </form>

        <table class="table table-striped table-ads-list">
            <thead>
                <tr>
                    {{-- <th>ID</th>  ← removed as requested --}}
                    <th>Title</th>
                    <th>Type</th>
                    <th>Group</th>
                    <th>Preview</th> {{-- Collage + hover panel --}}
                    <th>Weight</th>
                    <th>Status</th>
                    <th>
                        Impr.
                        @if ($tf != 'all')
                            <span class="text-muted small">({{ $tf == 'today' ? '1' : $tf }} d)</span>
                        @endif
                    </th>
                    <th>
                        Clicks
                        @if ($tf != 'all')
                            <span class="text-muted small">({{ $tf == 'today' ? '1' : $tf }} d)</span>
                        @endif
                    </th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>

                @foreach ($ads as $ad)
                    @php
                        // Use images defined on the assigned AdGroup
                        $group = $ad->groupRef ?? null; // relation: Ad belongsTo AdGroup
                        $imgs = $group && $group->images ? $group->images : collect();
                        $imgCount = $imgs->count();

                        // convenience fn for URL (supports absolute URLs too)
                        $resolveImg = function ($path) {
                            if (preg_match('~^https?://~i', $path ?? '')) {
                                return $path;
                            }
                            try {
                                return Storage::disk('wasabi')->url(ltrim($path ?? '', '/'));
                            } catch (\Throwable $e) {
                                return $path; // last-resort fallback
                            }
                        };

                        // Legacy fallback if needed (optional; remove if you don't want it)
if ($imgCount === 0 && !empty($ad->image)) {
    $imgs = collect([(object) ['image_url' => $ad->image]]);
                            $imgCount = 1;
                        }
                    @endphp
                    <tr>
                        {{-- <td class="align-middle">{{ $ad->id }}</td>  ← removed --}}
                        <td class="align-middle">{{ $ad->title }}</td>
                        <td class="align-middle">
                            @if ($ad->type == \App\Models\Ad::TYPE_ANNUNCIO_IMMAGINE)
                                IMAGE/URL
                            @else
                                GOOGLE ADS
                            @endif
                        </td>
                        <td class="align-middle">{{ $ad->group_name }}</td>

                        {{-- PREVIEW COL --}}
                        <td class="align-middle">
                            @if ($imgCount)
                                <div class="ad-preview">
                                    {{-- small collage --}}
                                    <div class="ad-collage" data-count="{{ $imgCount }}">
                                        @foreach ($imgs as $img)
                                            @php $src = $resolveImg($img->image_url); @endphp
                                            <span class="ad-collage-piece">
                                                <img src="{{ $src }}" alt="{{ $ad->title }}">
                                            </span>
                                        @endforeach
                                    </div>

                                    {{-- hover panel with big thumbnails (stacked rows) --}}
                                    <div class="ad-hover-panel">
                                        @foreach ($imgs as $img)
                                            @php $src = $resolveImg($img->image_url); @endphp
                                            <div class="ad-hover-row">
                                                <img src="{{ $src }}" alt="{{ $ad->title }}">
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @else
                                <span class="text-muted small">—</span>
                            @endif
                        </td>

                        <td class="align-middle">{{ $ad->getWeightPercentage() }}%</td>
                        <td class="align-middle">
                            @if ($ad->status)
                                Published
                            @else
                                Draft
                            @endif
                        </td>
                        <td class="align-middle">
                            {{ $ad->total_impressions ?? 0 }}
                        </td>
                        <td class="align-middle">
                            {{ $ad->total_clicks ?? 0 }}
                        </td>
                        <td class="align-middle">
                            <div class="d-flex gap-2">
                                <a href="{{ route('ads.edit', $ad->id) }}" class="btn btn-primary">Edit</a>
                                <form action="{{ route('ads.destroy', $ad->id) }}" method="post"
                                    onsubmit="return confirm('Delete this ad?');">
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

@push('footer')
    <style>
        /* --- tiny collage preview --------------------------------------- */
        .table-ads-list .ad-collage {
            width: 50px;
            height: 50px;
            display: flex;
            overflow: hidden;
            border: 1px solid rgba(0, 0, 0, .1);
            border-radius: 2px;
            background: #f8f9fa;
        }

        .table-ads-list .ad-collage-piece {
            flex: 1 1 auto;
            position: relative;
            overflow: hidden;
        }

        .table-ads-list .ad-collage-piece img {
            display: block;
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        /* --- preview wrapper + hover panel ------------------------------- */
        .ad-preview {
            position: relative;
            display: inline-block;
        }

        .ad-hover-panel {
            display: none;
            position: absolute;
            top: 0;
            left: 50px;
            /* sits right of the small collage */
            z-index: 10;
            min-width: 260px;
            max-width: 420px;
            max-height: 320px;
            overflow: auto;
            padding: 8px;
            background: #fff;
            border: 1px solid rgba(0, 0, 0, .15);
            border-radius: .25rem;
            box-shadow: 0 8px 24px rgba(0, 0, 0, .15);
        }

        .ad-preview:hover .ad-hover-panel {
            display: block;
        }

        .ad-hover-row {
            display: block;
            width: 100%;
            margin-bottom: 8px;
        }

        .ad-hover-row img {
            display: block;
            width: 100%;
            height: auto;
            /* keep aspect; each row its own image */
            border-radius: .25rem;
        }

        /* optional: keep panel within viewport horizontally */
        @media (max-width: 768px) {
            .ad-hover-panel {
                left: 0;
                top: 60px;
            }
        }
    </style>
@endpush
