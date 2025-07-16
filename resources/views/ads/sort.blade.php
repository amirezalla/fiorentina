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

    <form action="{{ route('ads.sort.update') }}" method="POST" id="sortForm">
        @csrf
        <input type="hidden" name="group" value="{{ $groupId }}">

        <div class="table-responsive">
            <table class="table table-striped align-middle table-ads-sort">
                <thead>
                    <tr>
                        <th>Ad&nbsp;ID</th>
                        <th>Title</th>
                        <th>Images</th>       {{-- NEW --}}
                        <th>Current&nbsp;Weight</th>
                        <th>New&nbsp;Weight</th>
                    </tr>
                </thead>
                <tbody>
                @forelse($ads as $ad)
                    @php
                        // make sure images are eager-loaded in the controller → with('images')
                        $imgs = $ad->images;
                        if ($imgs->isEmpty() && $ad->image) {
                            $imgs = collect([(object)['image_url'=>$ad->image]]);
                        }
                        $imgCount = $imgs->count();

                        $resolve = fn($p) => try {
                            return Storage::disk('wasabi')->temporaryUrl($p, now()->addMinutes(5));
                        } catch (\Throwable $e) {
                            return Storage::disk('wasabi')->url($p);
                        };
                    @endphp
                    <tr data-images="{{ $imgCount }}">
                        <td>{{ $ad->id }}</td>
                        <td>{{ $ad->title }}</td>

                        {{-- Images preview --}}
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
                        <td style="min-width:180px">
                            <div class="d-flex align-items-center">
                                <input type="range"
                                       class="form-range me-2 weight-slider"
                                       id="slider-{{ $ad->id }}"
                                       name="weights[{{ $ad->id }}]"
                                       min="1" max="10" step="1"
                                       value="{{ old("weights.$ad->id", $ad->weight) }}"
                                       onchange="updateSliderValue({{ $ad->id }})">
                                <span id="slider-value-{{ $ad->id }}">{{ old("weights.$ad->id", $ad->weight) }}</span>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5">No ads found for this group.</td></tr>
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
</div>
@endsection

@push('footer')
<style>
/* ------- tiny collage preview ---------------------------------- */
.table-ads-sort .ad-collage {
    width:50px;height:50px;display:flex;overflow:hidden;
    border:1px solid rgba(0,0,0,.1);border-radius:2px;background:#f8f9fa;
}
.table-ads-sort .ad-collage-piece {flex:1 1 auto;overflow:hidden}
.table-ads-sort .ad-collage-piece img {width:100%;height:100%;object-fit:cover}
</style>

<script>
/* ---------- helpers -------------------------------------------- */
function gcd(a,b){ return b ? gcd(b, a%b) : a; }
function decimalToFraction(x){
    const EPS=1e-6; if(Math.abs(x-Math.round(x))<EPS) return Math.round(x)+'';
    let denom=1; while(Math.abs(x*denom-Math.round(x*denom))>EPS && denom<=100) denom++;
    let num=Math.round(x*denom), g=gcd(num,denom);
    return (num/g)+"/"+(denom/g);
}

/* ---------- per-row slider handling ---------------------------- */
function updateSliderValue(adId){
    const slider=document.getElementById("slider-"+adId);
    document.getElementById("slider-value-"+adId).textContent=slider.value;
    updateSummary();
}

/* ---------- global summary ------------------------------------- */
function updateSummary(){
    let perImageWeights=[];
    document.querySelectorAll('tbody tr').forEach(tr=>{
        const imagesCnt=parseInt(tr.dataset.images||'1',10)||1;
        const weight=parseFloat(tr.querySelector('.weight-slider').value);
        const eff=weight/imagesCnt;          // weight that each single image receives
        for(let i=0;i<imagesCnt;i++) perImageWeights.push(eff);
    });

    const total=perImageWeights.reduce((a,b)=>a+b,0);
    const fractions=perImageWeights
        .map(w=>w/total)
        .map(decimalToFraction);

    document.getElementById('weight-summary').textContent =
        fractions.length
            ? 'Relative order (per image):  ' + fractions.join(' , ')
            : 'No images.';
}

/* run once on load */
document.addEventListener('DOMContentLoaded',updateSummary);
</script>
@endpush
