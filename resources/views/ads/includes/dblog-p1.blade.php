<div class="d-none d-md-block">
    @if (isset($ad) && $ad)
        @if ($ad->type == 1)
            {{-- Track impression for type=1 ads --}}
            @php
                \App\Models\AdStatistic::trackImpression($ad->id);
                $ad->increment('display_count');

            @endphp

            <div class="row justify-content-center mx-0 mb-2" id="p1-{{ $ad->id }}">
                <div class="col-12 mx-auto p-0">
                    {{-- Link through your trackClick route so that a click is counted --}}
                    @php $src = ad_slot_img('p1'); @endphp
                    @if ($src)
                        <div class="ads-slot" style="text-align:center;margin:12px 0;">
                            <a href="{{ ad_slot_href('p1') }}" target="_blank" rel="nofollow sponsored noopener">
                                <img src="{{ $src }}" alt="sponsored" class="img-fluid"
                                    style="max-width:100%;height:auto;">
                            </a>
                        </div>
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
