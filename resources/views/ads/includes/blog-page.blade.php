@if(isset($ads) && $ads)
    <div class="row mx-0">
        @foreach($ads as $ad)
            <div class="col-12 col-md-6 mb-4">
                <a href="" class="d-block">
                    <img src="{{ $ad->getOptimizedImageUrlAttribute() }}" alt="{{ $ad->title }}" class="img-fluid" style="width: 100%; height: auto;">
                </a>
            </div>
        @endforeach
    </div>
@endif



