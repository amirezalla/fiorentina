<div class="d-block d-md-none col-12 text-center">


    @if (isset($ad) && $ad)
        @if ($ad->type == 1)
            <div class="row justify-content-center mx-0">

                <div class="col-12 mx-auto">
                    <a href="" class="d-block">
                        <img src="{{ $ad->getImageUrl() }}" alt="{{ $ad->title }}" class="img-fluid"
                            @if (!$ad->width) style="width: 100%; height: auto;">
                @else
                    style="width: {{ $ad->width }}px; height: {{ $ad->height }}px;"> @endif
                            </a>
                </div>

            </div>
        @else
            <div class="row justify-content-center mx-0">
                {!! $ad->amp !!}
            </div>
        @endif
    @endif
</div>
