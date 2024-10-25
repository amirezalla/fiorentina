@if (isset($ads) && $ads)
    <div class="container">
        <div class="row mx-0">
            @foreach ($ads as $ad)
                @if ($ad->type == 1)
                    <div class="row justify-content-center mx-0">

                        <div class="col-12 mx-auto">
                            <a href="{{ $ad->url }}" class="d-block">
                                <img src="{{ $ad->getImageUrl() }}" alt="{{ $ad->title }}" class="img-fluid"
                                    style="width: 100%; height: auto;">
                            </a>
                        </div>
                    </div>
                @else
                    {!! $ad->amp !!}
                @endif
            @endforeach
        </div>
    </div>
@endif
