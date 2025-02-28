@php
    $isMobile = false;

    // Check for headers commonly associated with mobile devices
    if ($request->hasHeader('x-wap-profile') || $request->hasHeader('profile')) {
        $isMobile = true;
    } else {
        // Check if the Accept header indicates mobile/WAP content
        $accept = $request->header('Accept');
        if ($accept && stripos($accept, 'wap') !== false) {
            $isMobile = true;
        }
} @endphp

@if ($isMobile)
    @if (isset($ad) && $ad)
        @if ($ad->type == 1)
            <div class="row justify-content-center mx-0">

                <div class="col-12 mx-auto">
                    <a href="" class="d-block">
                        <img src="{{ $ad->getOptimizedImageUrlAttribute() }}" alt="{{ $ad->title }}" class="img-fluid"
                            @if (!$ad->width) style="width: 100%; height: auto;">
                @else
                    style="width: {{ $ad->width }}px; height: {{ $ad->height }}px;"> @endif
                            </a>
                </div>

            </div>
        @else
            {!! $ad->amp !!}
        @endif
    @endif

@endif
