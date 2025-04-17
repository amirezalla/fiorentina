@php
    use Carbon\Carbon;
    $date = Carbon::parse($post->published_at);
    $formattedDate = $date->locale('it')->translatedFormat('d F Y - H:i');
@endphp



@if ($post->author->name)
    <div class="row">
        <div class="col-lg-7" style="padding-top:6px">
            <span class="created_at " style="color: gray;">
                {!! BaseHelper::renderIcon('ti ti-clock') !!} {{ $formattedDate }}
            </span>
            @if ($post->author->avatar->url)
                <img class="post-author" src="{{ $post->author->avatar->url }}" alt="$post->author->avatar->url">
            @else
                <span class="post-author " style="color: gray;">{!! BaseHelper::renderIcon('ti ti-user-circle') !!}
            @endif
            <span class="author-name">{{ $post->author->name }}</span>

        </div>
        <div class="col-lg-5 d-flex justify-content-end pr-30" style="padding-bottom: 14px">
            <div class="social-buttons">
                <a href="#" class="social-btn facebook"><i class="fab fa-facebook-f"></i></a>
                <a href="#" class="social-btn twitter"><i class="fab fa-twitter"></i></a>
                <a href="#" class="social-btn pinterest"><i class="fab fa-pinterest-p"></i></a>
                <a href="#" class="social-btn email"><i class="fas fa-envelope"></i></a>
                <a href="#" class="social-btn comment-btn"><i class="fas fa-comment"></i></a>
            </div>
        </div>
    </div>



@endif

<div class="row">

    @include('ads.includes.adsrecentp1')



    <div class="col-lg-12 d-flex justify-content-center img-in-post mb-3 mt-3">
        <div>


            {{ RvMedia::image($post->image, $post->name, 'featured', attributes: ['loading' => 'lazy', 'style' => 'width:775px;height:475px;']) }}
        </div>
    </div>

    @include('ads.includes.adsrecentp2')
    <div class="d-block d-md-none col-12 text-center">
        @include('ads.includes.MOBILE_DOPO_FOTO_26')
    </div>

</div>
