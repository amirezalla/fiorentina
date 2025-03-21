@if (isset($ads) && $ads)
    <div class="row mx-0 dmnone ">
        @foreach ($ads as $ad)
            @if ($ad->type == 1)
                <div class="row justify-content-center mx-0">

                    <div class="col-12 mx-auto">
                        <a href="" class="d-block">
                            <img src="{{ $ad->getOptimizedImageUrlAttribute() }}" alt="{{ $ad->title }}"
                                class="img-fluid" style="width: 100%; height: auto;">
                        </a>
                    </div>

                </div>
            @else
                <div class="row justify-content-center mx-0">
                    {!! $ad->amp !!}
                </div>
            @endif
        @endforeach
    </div>
@endif
