@if (isset($ad) && $ad)
    @if ($ad->type == 1)
        <div class="row justify-content-center mx-0">

            <div class="col-12 mx-auto">
                <a href="" class="d-block">
                    <img src="{{ $ad->getImageUrl() }}" alt="{{ $ad->title }}" class="img-fluid"
                    @if(!$post->width)
                        style="width: 100%; height: auto;">
                    @else
                        style="width: {{ $post->width }}px; height: {{ $post->height }}px;">
                    @endif
                </a>
            </div>

        </div>
    @else
        {!! $ad->amp !!}
    @endif
@endif
