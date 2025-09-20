@if (isset($ad) && $ad)
    <style>
        .fixed-ad-container {
            background-color: red;
            position: sticky;
            top: 110px;
            bottom: 17px display: none;
        }

        @media only screen and (min-width: 1830px) {
            .fixed-ad-container {
                display: -webkit-box;
                display: -webkit-flex;
                display: -ms-flexbox;
                display: flex;
            }
        }
    </style>
    <div class="fixed-ad-container justify-content-center">
        <div class="w-100 d-flex justify-content-center">
            <div class="position-absolute" style="top:-32px">
                <a href="" class="d-flex w-100 linkofit">
                    <img src="{{ $ad->getOptimizedImageUrlAttribute() }}" alt="{{ $ad->title }}" class="w-full d-block"
                        style="max-width: none;margin-right:-1px">
                </a>
            </div>
        </div>
    </div>
@endif
