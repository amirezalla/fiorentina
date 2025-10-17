<div class="d-none d-md-block">
    @if (isset($ad) && $ad)
        @if ($ad->type == 1)
            {{-- Track impression for type=1 ads --}}
            @php
                \App\Models\AdStatistic::trackImpression($ad->id);
                $ad->increment('display_count');

            @endphp

            <div class="row justify-content-center mx-0 mb-2">
                <div class="col-12 mx-auto p-0">
                    {{-- Link through your trackClick route so that a click is counted --}}
                    @if ($img)
                        <a href="{{ $href }}" class="d-block" rel="nofollow sponsored noopener" target="_blank">
                            <img src="{{ $img }}" alt="{{ $ad->title }}" class="img-fluid"
                                style="width:100%;height:auto;">
                        </a>
                    @endif
                </div>
            </div>
        @else
            {{-- For type=2 (Google Ad Manager or custom HTML), no impression is tracked here --}}
            <div class="row justify-content-left mx-0 mb-2">
                {!! $ad->amp !!}
            </div>
        @endif
    @endif
</div>
