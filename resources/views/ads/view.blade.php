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
                        <option value="today" @selected($tf == 'today')>Today</option>
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

        <table class="table table-striped table-ads-list align-middle">
            <thead>
                <tr>
                    {{-- ID removed --}}
                    <th>Title</th>
                    <th>Type</th>
                    <th>Group</th>
                    <th>Preview</th>
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
                    <th>Expiry</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>

                @foreach ($ads as $ad)
                    @php
                        // Compute status pill (including Expired)
                        $now = \Carbon\Carbon::today();
                        $expiry = $ad->expiry_date ? \Carbon\Carbon::parse($ad->expiry_date) : null;
                        $isExpired = $expiry && $expiry->lt($now);

                        // icons
                        $isGoogle = $ad->type == \App\Models\Ad::TYPE_GOOGLE_ADS;
                        $typeIconHtml = $isGoogle
                            ? '<i class="fa-brands fa-google me-1"></i> Google Ads'
                            : '<i class="fa-regular fa-image me-1"></i> Image/URL';

                        // For image ads, get images from group (shared) or legacy fallback
                        $group = $ad->groupRef ?? null; // define belongsTo AdGroup in model (groupRef)
                        $imgs = $group && $group->images ? $group->images : collect();
                        $imgCount = $imgs->count();

                        if ($imgCount === 0 && !empty($ad->image)) {
                            $imgs = collect([(object) ['image_url' => $ad->image]]);
                            $imgCount = 1;
                        }

                        $resolveImg = function ($path) {
                            if (preg_match('~^https?://~i', $path ?? '')) {
                                return $path;
                            }
                            try {
                                return Storage::disk('wasabi')->url(ltrim($path ?? '', '/'));
                            } catch (\Throwable $e) {
                                return $path ?: '';
                            }
                        };
                    @endphp
                    <tr>
                        <td>{{ $ad->title }}</td>

                        {{-- Type with icon --}}
                        <td>{!! $typeIconHtml !!}</td>

                        <td>{{ $ad->group_name }}</td>

                        {{-- PREVIEW --}}
                        <td>
                            @if ($isGoogle && $ad->amp)
                                @php
                                    $amp = $ad->amp ?? '';
                                    $w =
                                        $ad->width ?:
                                        (preg_match('/\bwidth\s*=\s*"(\d+)"/i', $amp, $m)
                                            ? (int) $m[1]
                                            : 300);
                                    $h =
                                        $ad->height ?:
                                        (preg_match('/\bheight\s*=\s*"(\d+)"/i', $amp, $m)
                                            ? (int) $m[1]
                                            : 250);
                                    $slot = preg_match('/data-slot\s*=\s*"([^"]+)"/i', $amp, $m) ? $m[1] : '—';
                                    $thumbW = min($w, 160);
                                    $thumbH = (int) round($h * ($thumbW / max(1, $w)));
                                @endphp

                                <div class="preview-wrapper d-inline-block">
                                    <div class="gam-thumb d-inline-flex align-items-center justify-content-center flex-column"
                                        style="width:50px;height:50px">
                                        <span class="text-muted small"><i class="fa-brands fa-google me-1"></i>
                                            Google</span>
                                    </div>

                                    {{-- Hover button that reveals a panel with an iframe live preview --}}
                                    <div class="mt-1">

                                        <div class="hover-preview-panel">
                                            <div class="ratio ratio-16x9 mb-2" style="min-width: 360px; max-width: 560px;">
                                                <iframe src="{{ route('ads.ampPreview', $ad) }}" loading="lazy"
                                                    referrerpolicy="no-referrer-when-downgrade"
                                                    style="border:0;border-radius:.25rem;"></iframe>
                                            </div>
                                            <div class="text-muted xsmall">
                                                Preview may not always render an actual ad
                                                (depends on GAM setup / domain allowlist / blockers).
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @else
                                {{-- IMAGE/URL ads → collage + hover panel --}}
                                @if ($imgCount)
                                    <div class="ad-preview">
                                        <div class="ad-collage" data-count="{{ $imgCount }}">
                                            @foreach ($imgs as $img)
                                                @php $src = $resolveImg($img->image_url); @endphp
                                                <span class="ad-collage-piece">
                                                    <img src="{{ $src }}" alt="{{ $ad->title }}">
                                                </span>
                                            @endforeach
                                        </div>
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
                            @endif
                        </td>

                        <td>{{ $ad->getWeightPercentage() }}%</td>

                        {{-- Status pill (Published / Draft / Expired) --}}
                        <td>
                            @if ($isExpired)
                                <span class="badge bg-danger">Expired</span>
                            @else
                                @if ($ad->status)
                                    <span class="badge bg-success">Published</span>
                                @else
                                    <span class="badge bg-warning text-dark">Draft</span>
                                @endif
                            @endif
                        </td>

                        <td>{{ $ad->total_impressions ?? 0 }}</td>
                        <td>{{ $ad->total_clicks ?? 0 }}</td>

                        {{-- Expiry column --}}
                        <td>
                            @if ($ad->expiry_date)
                                <span class="{{ $isExpired ? 'text-danger' : '' }}">
                                    {{ \Carbon\Carbon::parse($ad->expiry_date)->toDateString() }}
                                </span>
                            @else
                                <span class="text-muted">—</span>
                            @endif
                        </td>

                        <td>
                            <div class="d-flex gap-2">
                                <a href="{{ route('ads.edit', $ad->id) }}" class="btn btn-primary btn-sm">Edit</a>
                                <form action="{{ route('ads.destroy', $ad->id) }}" method="post"
                                    onsubmit="return confirm('Delete this ad?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm">Delete</button>
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

        /* --- image preview hover panel ---------------------------------- */
        .ad-preview {
            position: relative;
            display: inline-block;
        }

        .ad-hover-panel {
            display: none;
            position: absolute;
            top: 0;
            left: 60px;
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
            border-radius: .25rem;
        }

        /* --- GAM thumb + hover preview panel ----------------------------- */
        .gam-thumb {
            border: 1px dashed rgba(0, 0, 0, .2);
            border-radius: .25rem;
            background: #fafafa;
            padding: .25rem .5rem;
            text-align: center;
        }

        .gam-thumb .xsmall {
            font-size: .7rem;
        }

        .preview-wrapper {
            position: relative;
        }

        .hover-preview-trigger {
            position: relative;
        }

        .hover-preview-panel {
            display: none;
            position: absolute;
            left: 0;
            top: calc(100% + 6px);
            background: #fff;
            border: 1px solid rgba(0, 0, 0, .15);
            border-radius: .25rem;
            box-shadow: 0 8px 24px rgba(0, 0, 0, .15);
            padding: 10px;
            z-index: 20;
        }

        .preview-wrapper:hover .hover-preview-panel {
            display: block;
        }

        /* Keep panel within viewport on narrow screens */
        @media (max-width: 768px) {
            .ad-hover-panel {
                left: 0;
                top: 60px;
            }

            .hover-preview-panel {
                max-width: 90vw;
            }
        }
    </style>
@endpush
