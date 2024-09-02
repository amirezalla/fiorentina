@php
    use Carbon\Carbon;
    $date = Carbon::parse($post->created_at);
    $formattedDate = $date->format('j F Y - H:i');

@endphp



@if ($post->author->name)
    <div class="row">
        <div class="col-lg-6">
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
        <div class="col-lg-6 d-flex justify-content-end pr-30">
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
    @include('ads.includes.dblog-author')
</div>

<div>
    {{ RvMedia::image($post->image, $post->name, 'featured', attributes: ['loading' => 'eager']) }}
</div>
<div class="row">
    @include('ads.includes.dblog-title')
</div>
