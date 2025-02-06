@php Theme::set('section-name', $category->name) @endphp

@if ($posts->isNotEmpty())
    @foreach ($posts->loadMissing('author') as $post)
        <article class="post post__horizontal mb-40 clearfix">
            <div class="post__thumbnail">
                {{ RvMedia::image($post->image, $post->name, 'thumbnail') }}
                <a href="{{ $post->url }}" title="{{ $post->name }}" class="post__overlay"></a>
            </div>
            <div class="post__content-wrap">
                <header class="post__header">
                    <h3 class="post__title"><a href="{{ $post->url }}"
                            title="{{ $post->name }}">{{ $post->name }}</a></h3>

                    <div class="post__content">
                        <p data-number-line="4">{{ $post->description }}</p>
                        <span class=" text-dark mt-3 d-block">
                            @php
                                $post->comments_count = FriendsOfBotble\Comment\Models\Comment::where(
                                    'reference_id',
                                    $post->id,
                                )->count();
                            @endphp
                            Di <span class=" fw-bold author-post" style="color:#8424e3">{{ $post->author->first_name }}
                                {{ $post->author->last_name }}</span> /
                            <a class="fw-bold" href="{{ $post->url }}#comments" style="color:#8424e3">
                                <i class="fa fa-comment" aria-hidden="true"></i>
                                {{ $post->comments_count > 0 ? $post->comments_count : 'Commenta' }}
                            </a>
                        </span>
                    </div>
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
