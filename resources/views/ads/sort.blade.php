{{-- resources/views/ads/sort.blade.php --}}
@extends(BaseHelper::getAdminMasterLayoutTemplate())

@section('content')
    @php
        // Default mode: if exactly one ad in this group → images tab, else ads tab
        $mode = request('mode');
        if (!$mode) {
            $mode = $ads->count() === 1 ? 'images' : 'ads';
        }

        // If images mode, pick the single ad and its images
        $singleAd = $ads->count() === 1 ? $ads->first() : null;
        $imageItems = $singleAd?->images ?? collect();
    @endphp

    <div class="container">
        <h1 class="mb-4">Sort Ads for Group: {{ $groupName }}</h1>

        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if (session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        {{-- Tabs --}}
        <ul class="nav nav-tabs mb-3">
            <li class="nav-item">
                <a class="nav-link {{ $mode === 'ads' ? 'active' : '' }}"
                    href="{{ route('ads.sort', ['group' => $groupId, 'mode' => 'ads']) }}">
                    By Ads
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ $mode === 'images' ? 'active' : '' }}"
                    href="{{ route('ads.sort', ['group' => $groupId, 'mode' => 'images']) }}">
                    By Images (single ad)
                </a>
            </li>
        </ul>

        @if ($mode === 'images')
            {{-- =========================
             By Images (single ad)
        ========================== --}}
            @if (!$singleAd)
                <div class="alert alert-warning">
                    This tab is available when the selected group contains exactly one ad.
                </div>
            @else
                <div class="mb-2">
                    <strong>Ad:</strong> {{ $singleAd->title }}
                </div>

                <form action="{{ route('ads.sort.update') }}" method="POST" id="sortImagesForm">
                    @csrf
                    <input type="hidden" name="group" value="{{ $groupId }}">
                    <input type="hidden" name="mode" value="images">
                    <input type="hidden" name="ad_id" value="{{ $singleAd->id }}">

                    <div class="table-responsive">
                        <table class="table table-striped align-middle">
                            <thead>
                                <tr>
                                    <th style="width:160px">Image</th>
                                    <th>Link</th>
                                    <th style="min-width:220px">Weight</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($imageItems as $img)
                                    @php
                                        // Start from the raw value
                                        $src = $img->image_url ?? '';

                                        // If it's not an absolute URL, try to turn the storage key into a URL
if (!preg_match('~^https?://~i', $src)) {
    try {
        $src = Storage::disk('wasabi')->url($src);
    } catch (\Throwable $e) {
        // leave $src as-is on failure
    }
}

// Link + weight defaults (clamped 1..10)
$linkVal = old("image_links.$img->id", $img->target_url ?? '');
                                        $weightVal = (int) old("image_weights.$img->id", $img->weight ?? 1);
                                        $weightVal = max(1, min(10, $weightVal));
                                    @endphp

                                    <tr>
                                        <td>
                                            <img src="{{ $src }}" alt=""
                                                style="width:150px;height:90px;object-fit:cover;border:1px solid rgba(0,0,0,.1)">
                                        </td>
                                        <td>
                                            <input type="url" name="image_links[{{ $img->id }}]"
                                                class="form-control" placeholder="https://example.com"
                                                value="{{ $linkVal }}">
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <input type="range" class="form-range me-2 img-weight-slider"
                                                    name="image_weights[{{ $img->id }}]" min="1" max="10"
                                                    step="1" value="{{ $weightVal }}"
                                                    oninput="document.getElementById('img-wval-{{ $img->id }}').textContent=this.value">
                                                <span id="img-wval-{{ $img->id }}">{{ $weightVal }}</span>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3">No images found for this ad.</td>
                                    </tr>
                                @endforelse

                            </tbody>
                        </table>
                    </div>

                    <button type="submit" class="btn btn-primary">Save Image Weights</button>
                </form>
            @endif
        @else
            {{-- =========================
                 By Ads
        ========================== --}}
            <form action="{{ route('ads.sort.update') }}" method="POST" id="sortAdsForm">
                @csrf
                <input type="hidden" name="group" value="{{ $groupId }}">
                <input type="hidden" name="mode" value="ads">

                <div class="table-responsive">
                    <table class="table table-striped align-middle table-ads-sort">
                        <thead>
                            <tr>
                                <th>Ad&nbsp;ID</th>
                                <th>Title</th>
                                <th>Label</th>
                                <th>Images</th>
                                <th>Current&nbsp;Weight</th>
                                <th>New&nbsp;Weight</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($ads as $ad)
                                @php
                                    $imgs = $ad->images ?? collect();
                                    if ($imgs->isEmpty() && $ad->image) {
                                        // legacy fallback
                                        $imgs = collect([(object) ['image_url' => $ad->image]]);
                                    }
                                    $imgCount = $imgs->count();

                                    $resolve = function ($path) {
                                        if (preg_match('~^https?://~i', $path ?? '')) {
                                            return $path;
                                        }
                                        try {
                                            return Storage::disk('wasabi')->url($path);
                                        } catch (\Throwable $e) {
                                            return $path;
                                        }
                                    };

                                    $newWeight = (int) old("weights.$ad->id", $ad->weight);
                                    $newWeight = max(1, min(10, $newWeight));
                                @endphp
                                <tr data-images="{{ $imgCount }}">
                                    <td>{{ $ad->id }}</td>
                                    <td>{{ $ad->title }}</td>
                                    <td>
                                        @if ($ad->label)
                                            <span class="badge bg-secondary">{{ $ad->label }}</span>
                                        @else
                                            <span class="text-muted">—</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if ($imgCount)
                                            <div class="ad-collage" data-count="{{ $imgCount }}">
                                                @foreach ($imgs as $im)
                                                    <span class="ad-collage-piece">
                                                        <img src="{{ $resolve($im->image_url) }}" alt="">
                                                    </span>
                                                @endforeach
                                            </div>
                                        @else
                                            <span class="text-muted small">—</span>
                                        @endif
                                    </td>
                                    <td>{{ (int) $ad->weight }}</td>
                                    <td style="min-width:200px">
                                        <div class="d-flex align-items-center">
                                            <input type="range" class="form-range me-2 weight-slider"
                                                id="slider-{{ $ad->id }}" name="weights[{{ $ad->id }}]"
                                                min="1" max="10" step="1" value="{{ $newWeight }}"
                                                onchange="updateSliderValue({{ $ad->id }})">
                                            <span id="slider-value-{{ $ad->id }}">{{ $newWeight }}</span>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6">No ads found for this group.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <button type="submit" class="btn btn-primary">Update Weights</button>

                <div class="alert alert-info mt-3">
                    <small>
                        Note: When an ad has multiple images, its total weight is divided evenly among those images.
                        The live summary below shows the <strong>relative share</strong> each single image will receive
                        if you save the current slider settings.
                    </small>
                </div>

                <div id="weight-summary" class="alert alert-secondary mt-3"></div>
            </form>
        @endif
    </div>
@endsection

@push('footer')
    <style>
        /* tiny collage preview */
        .table-ads-sort .ad-collage {
            width: 50px;
            height: 50px;
            display: flex;
            overflow: hidden;
            border: 1px solid rgba(0, 0, 0, .1);
            border-radius: 2px;
            background: #f8f9fa;
        }

        .table-ads-sort .ad-collage-piece {
            flex: 1 1 auto;
            overflow: hidden;
        }

        .table-ads-sort .ad-collage-piece img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
    </style>

    @if ($mode === 'ads')
        <script>
            function updateSliderValue(adId) {
                const el = document.getElementById('slider-' + adId);
                const out = document.getElementById('slider-value-' + adId);
                if (el && out) out.textContent = el.value;
                updateSummary();
            }

            function updateSummary() {
                let perImageWeights = [];
                document.querySelectorAll('tbody tr').forEach(tr => {
                    const imgCount = parseInt(tr.dataset.images || '1', 10) || 1;
                    const slider = tr.querySelector('.weight-slider');
                    if (!slider) return;
                    const weight = parseFloat(slider.value);
                    const eff = (imgCount > 0) ? weight / imgCount : weight;
                    for (let i = 0; i < Math.max(1, imgCount); i++) perImageWeights.push(eff);
                });
                const total = perImageWeights.reduce((a, b) => a + b, 0) || 1;
                const percentages = perImageWeights.map(w => (w / total * 100).toFixed(1).replace(/\.0$/, '') + '%');
                const el = document.getElementById('weight-summary');
                if (el) el.textContent = 'Relative share (per image): ' + percentages.join(' , ');
            }

            document.addEventListener('DOMContentLoaded', updateSummary);
            document.addEventListener('input', (e) => {
                if (e.target.classList.contains('weight-slider')) {
                    const id = e.target.id.replace('slider-', '');
                    updateSliderValue(id);
                }
            });
        </script>
    @else
        <script>
            /* show live numeric values on image sliders */
            document.addEventListener('input', function(e) {
                if (e.target && e.target.classList.contains('img-weight-slider')) {
                    const spanId = e.target.getAttribute('oninput')?.match(/img-wval-[0-9]+/)?.[0];
                    if (spanId) {
                        const span = document.getElementById(spanId);
                        if (span) span.textContent = e.target.value;
                    }
                }
            });
        </script>
    @endif
@endpush
