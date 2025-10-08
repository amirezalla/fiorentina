{{-- resources/views/ads/sort.blade.php --}}
@extends(BaseHelper::getAdminMasterLayoutTemplate())

@section('content')
    <div class="container">
        <h1 class="mb-4">Sort Ads for Group: {{ $groupName }}</h1>

        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if (session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        {{-- Tabs --}}
        @php $mode = $mode ?? 'ads'; @endphp
        <ul class="nav nav-tabs mb-3">
            <li class="nav-item">
                <a class="nav-link {{ $mode === 'ads' ? 'active' : '' }}"
                   href="{{ route('ads.sort', ['group' => $groupId, 'mode' => 'ads']) }}">
                    By Ads
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ $mode === 'labels' ? 'active' : '' }}"
                   href="{{ route('ads.sort', ['group' => $groupId, 'mode' => 'labels']) }}">
                    By Labels
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ $mode === 'group' ? 'active' : '' }}"
                   href="{{ route('ads.sort', ['group' => $groupId, 'mode' => 'group']) }}">
                    By Group (equalize)
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ $mode === 'images' ? 'active' : '' }} {{ $adsCount === 1 ? '' : 'disabled' }}"
                   href="{{ $adsCount === 1 ? route('ads.sort', ['group' => $groupId, 'mode' => 'images']) : '#' }}"
                   @if($adsCount !== 1) tabindex="-1" aria-disabled="true" @endif>
                    By Images (single ad)
                </a>
            </li>
        </ul>

        {{-- ===========================
             MODE: BY LABELS
        ============================ --}}
        @if ($mode === 'labels')
            <form action="{{ route('ads.sort.update') }}" method="POST" id="sortFormLabels">
                @csrf
                <input type="hidden" name="group" value="{{ $groupId }}">
                <input type="hidden" name="mode" value="labels">

                <div class="table-responsive">
                    <table class="table table-striped align-middle">
                        <thead>
                            <tr>
                                <th style="min-width:220px">Label</th>
                                <th>Ads in Label</th>
                                <th>Current Sum</th>
                                <th style="min-width:240px">New Sum</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($labels as $label => $info)
                                @php
                                    $curr = (int) ($info['weight'] ?? 0);
                                    $count = (int) ($info['count'] ?? 0);
                                    $safeName = $label;
                                    $initial = old("label_weights.$safeName", $curr);
                                @endphp
                                <tr>
                                    <td><span class="badge bg-light text-dark">{{ $label !== '' ? $label : '—' }}</span></td>
                                    <td>{{ $count }}</td>
                                    <td><strong>{{ $curr }}</strong></td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <input type="range" class="form-range me-2 label-slider"
                                                   min="0" max="100000" step="1"
                                                   value="{{ $initial }}"
                                                   data-target="lblval-{{ md5($label) }}"
                                                   name="label_weights[{{ $safeName }}]">
                                            <span id="lblval-{{ md5($label) }}">{{ $initial }}</span>
                                        </div>
                                        <small class="text-muted">0 mutes this label (all ads become weight 0)</small>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4">No labels found in this group.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mb-3 mt-3">
                    <button type="submit" class="btn btn-primary">Update Label Weights</button>
                </div>
            </form>

            <div class="alert alert-info mt-3">
                <small>
                    Changing a label’s <b>New Sum</b> scales the weights of all ads with that label proportionally,
                    rounding to integers and preserving the exact total you set.
                    If you set a label to 0, all its ads get weight 0.
                </small>
            </div>
        @endif

        {{-- ===========================
             MODE: BY GROUP (EQUALIZE)
        ============================ --}}
        @if ($mode === 'group')
            <form action="{{ route('ads.sort.update') }}" method="POST" class="mb-3">
                @csrf
                <input type="hidden" name="group" value="{{ $groupId }}">
                <input type="hidden" name="mode" value="group">

                <div class="card">
                    <div class="card-body row g-3 align-items-end">
                        <div class="col-12 col-md-4">
                            <label class="form-label">Set Group Total Weight</label>
                            <input type="number" name="total_weight" class="form-control" min="0" step="1"
                                   value="{{ old('total_weight', $ads->sum('weight')) }}">
                            <small class="text-muted">This total will be divided equally across all ads in this group.</small>
                        </div>
                        <div class="col-12 col-md-3">
                            <button class="btn btn-primary">Distribute equally</button>
                        </div>
                    </div>
                </div>
            </form>

            {{-- snapshot of current ads --}}
            <div class="table-responsive">
                <table class="table table-striped align-middle">
                    <thead>
                        <tr>
                            <th>Ad ID</th>
                            <th>Title</th>
                            <th>Current Weight</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($ads as $ad)
                            <tr>
                                <td>{{ $ad->id }}</td>
                                <td>{{ $ad->title }}</td>
                                <td>{{ $ad->weight }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="3">No ads found.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        @endif

        {{-- ===========================
             MODE: BY ADS (MANUAL)
        ============================ --}}
        @if ($mode === 'ads')
            <form action="{{ route('ads.sort.update') }}" method="POST" id="sortForm">
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
                            @forelse($ads as $ad)
                                @php
                                    // Prefer group images; fallback to legacy ad->image
                                    $group   = $ad->groupRef ?? null;
                                    $imgs    = ($group && $group->images) ? $group->images : collect();
                                    if ($imgs->isEmpty() && $ad->image) {
                                        $imgs = collect([(object) ['image_url' => $ad->image]]);
                                    }
                                    $imgCount = $imgs->count();
                                    $resolve = function ($path) {
                                        if (preg_match('~^https?://~i', $path ?? '')) return $path;
                                        try {
                                            return Storage::disk('wasabi')->url(ltrim($path ?? '', '/'));
                                        } catch (\Throwable $e) {
                                            return $path;
                                        }
                                    };
                                @endphp
                                <tr data-images="{{ $imgCount }}">
                                    <td>{{ $ad->id }}</td>
                                    <td>{{ $ad->title }}</td>
                                    <td>
                                        @if ($ad->label)
                                            <span class="badge bg-secondary">{{ $ad->label }}</span>
                                        @else
                                            <span class="text-muted small">—</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if ($imgCount)
                                            <div class="ad-collage" data-count="{{ $imgCount }}">
                                                @foreach ($imgs as $img)
                                                    <span class="ad-collage-piece">
                                                        <img src="{{ $resolve($img->image_url) }}" alt="">
                                                    </span>
                                                @endforeach
                                            </div>
                                        @else
                                            <span class="text-muted small">—</span>
                                        @endif
                                    </td>
                                    <td>{{ $ad->weight }}</td>
                                    <td style="min-width:200px">
                                        <div class="d-flex align-items-center">
                                            <input type="range" class="form-range me-2 weight-slider"
                                                   id="slider-{{ $ad->id }}" name="weights[{{ $ad->id }}]"
                                                   min="0" max="100000" step="1"
                                                   value="{{ old("weights.$ad->id", (int)$ad->weight) }}"
                                                   onchange="updateSliderValue({{ $ad->id }})">
                                            <span id="slider-value-{{ $ad->id }}">{{ old("weights.$ad->id", (int)$ad->weight) }}</span>
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

                <div class="mb-3 mt-3">
                    <button type="submit" class="btn btn-primary">Update Weights</button>
                </div>
            </form>

            <div class="alert alert-info mt-3">
                <small>
                    Note: When an ad has multiple images, its total weight is divided evenly among those images.
                    The live summary below shows the <strong>relative share</strong> each single image will receive
                    if you save the current slider settings.
                </small>
            </div>

            {{-- live explanation --}}
            <div id="weight-summary" class="alert alert-secondary mt-3"></div>
        @endif

        {{-- ===========================
             MODE: BY IMAGES (SINGLE AD)
        ============================ --}}
        @if ($mode === 'images' && $adsCount === 1)
            @php
                $theAd   = $ads->first();
                $images  = $theAd->groupRef?->images ?? collect();
                $resolve = fn($p) => preg_match('~^https?://~i', $p ?? '') ? $p : Storage::disk('wasabi')->url(ltrim($p ?? '', '/'));
            @endphp

            <form action="{{ route('ads.sort.update') }}" method="POST" class="mt-3">
                @csrf
                <input type="hidden" name="group" value="{{ $groupId }}">
                <input type="hidden" name="mode" value="images">

                <div class="table-responsive">
                    <table class="table table-striped align-middle">
                        <thead>
                            <tr>
                                <th>Image</th>
                                <th>Link</th>
                                <th style="min-width:220px">Weight</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($images as $img)
                                <tr>
                                    <td style="width:140px">
                                        <img src="{{ $resolve($img->image_url) }}" alt=""
                                             style="width:120px;height:80px;object-fit:cover;border-radius:.25rem;">
                                    </td>
                                    <td>
                                        @if (!empty($img->target_url))
                                            <a href="{{ $img->target_url }}" target="_blank" rel="noopener">{{ $img->target_url }}</a>
                                        @else
                                            <span class="text-muted">—</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <input type="range" class="form-range me-2"
                                                   name="image_weights[{{ $img->id }}]"
                                                   min="0" max="100000" step="1"
                                                   value="{{ old('image_weights.'.$img->id, (int)($img->weight ?? 1)) }}"
                                                   oninput="this.nextElementSibling.textContent=this.value">
                                            <span>{{ old('image_weights.'.$img->id, (int)($img->weight ?? 1)) }}</span>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="3">No images in this group.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <button class="btn btn-primary mt-3">Save Image Weights</button>
            </form>

            <div class="alert alert-info mt-3">
                <small>
                    These image weights are stored on the group and shared by any ad using this group.
                </small>
            </div>
        @endif
    </div>
@endsection

@push('footer')
    <style>
        /* ------- tiny collage preview ---------------------------------- */
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
            overflow: hidden
        }
        .table-ads-sort .ad-collage-piece img {
            width: 100%;
            height: 100%;
            object-fit: cover
        }
    </style>

    @if (($mode ?? 'ads') === 'ads')
        <script>
            function updateSliderValue(adId) {
                document.getElementById('slider-value-' + adId).textContent =
                    document.getElementById('slider-' + adId).value;
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
        </script>
    @elseif (($mode ?? '') === 'labels')
        <script>
            // Labels mode: live reflect slider values
            document.addEventListener('input', function(e) {
                if (e.target && e.target.classList.contains('label-slider')) {
                    const id = e.target.getAttribute('data-target');
                    const span = document.getElementById(id);
                    if (span) span.textContent = e.target.value;
                }
            });
        </script>
    @endif
@endpush
