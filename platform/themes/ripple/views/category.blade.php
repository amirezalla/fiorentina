@php Theme::set('section-name', $category->name) @endphp

@if ($posts->isNotEmpty())
    @foreach ($posts->loadMissing('author') as $post)
        <article class="post post__horizontal mb-40 clearfix">
            <div class="post__thumbnail">
                {{ RvMedia::image($post->image, $post->name, 'thumbnail') }}
                <a href="{{ $post->url }}" title="{{ $post->name }}" class="post__overlay"></a>
            </div>

        </article>
    @endforeach
    <div class="page-pagination text-right">
        {!! $posts->links() !!}
    </div>
@else
    <div class="alert alert-warning">
        <p class="mb-0">{{ __('There is no data to display!') }}</p>
    </div>
@endif
