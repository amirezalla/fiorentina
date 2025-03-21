@if (isset($ad) && $ad)
    <div class="container">
        <div class="row mx-0 justify-content-end">
            @if ($ad->type == 1)
                <div class="row justify-content-center mx-0">

                    <div class="col-8 mx-auto">
                        <a href="{{ $ad->url }}" class="d-block">
                            <img src="{{ $ad->getOptimizedImageUrlAttribute() }}" alt="{{ $ad->title }}"
                                class="img-fluid" style="width: 100%; height: auto;">
                        </a>
                    </div>
                </div>
            @else
                {!! $ad->amp !!}
            @endif
        </div>
    </div>
@endif
