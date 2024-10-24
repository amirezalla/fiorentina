@if (isset($ad) && $ad)
    @if ($ad->type == 1)
        <div class="row justify-content-center mx-0">

            <div class="col-12 col-md-8 col-lg-6 mx-auto">
                <a href="" class="d-block">
                    <img src="{{ $ad->getImageUrl() }}" alt="{{ $ad->title }}" class="img-fluid"
                        style="width: 100%; height: auto;">
                </a>
            </div>

        </div>
    @else
        <ins class="adsbygoogle" style="display:block" data-ad-client="ca-pub-166632497"
            data-ad-slot="{{ $ad->image }}" data-ad-format="auto" data-full-width-responsive="true"></ins>
    @endif
@endif
