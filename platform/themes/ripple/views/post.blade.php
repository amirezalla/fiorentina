@php
    Theme::set('section-name', $post->name);
    $post->loadMissing('metadata');

    if ($bannerImage = $post->getMetaData('banner_image', true)) {
        Theme::set('breadcrumbBannerImage', RvMedia::getImageUrl($bannerImage));
    }
    $content = \App\Models\Ad::addAdsToContent($post->content);

@endphp

<article class="post post--single">
    <header class="post__header" style="padding-top: 20px">
        @php
            $category = \Illuminate\Support\Facades\DB::connection('mysql')
                ->table('post_categories')
                ->where('post_id', $post->id)
                ->first();
            if ($category) {
                $cat = \Illuminate\Support\Facades\DB::connection('mysql')
                    ->table('categories')
                    ->where('id', $category->category_id)
                    ->first();
            }
        @endphp
        {{-- @if ($post->first_category?->name)
            <span class="post-category"
                style="display: block;
            width: fit-content;
            margin-bottom: 10px;">
                <a class="category-label" href="{{ $post->first_category->url }}">{{ $post->first_category->name }}</a>
            </span>
        @endif
        @if ($cat)
            <span class="post-category" style="display: block;
width: fit-content;
margin-bottom: 10px;">
                <a class="category-label" href="{{ $cat->url }}">{{ $cat->name }}</a>
            </span>
        @endif --}}
        <h1 class="post__title post__title_in">{{ $post->name }}</h1>
        <div class="post__meta">
            {!! Theme::partial('blog.post-meta', compact('post')) !!}

            @if ($post->tags->isNotEmpty())
                @php
                    if (is_plugin_active('language') && is_plugin_active('language-advanced')) {
                        $post->tags->loadMissing('translations');
                    }
                @endphp
            @endif
        </div>
    </header>
    <div class="post__content">
        @if (defined('GALLERY_MODULE_SCREEN_NAME') && !empty(($galleries = gallery_meta_data($post))))
            {!! render_object_gallery($galleries, $post->first_category ? $post->first_category->name : __('Uncategorized')) !!}
        @endif
        <div class="ck-content" style="color:black">{!! BaseHelper::clean($content) !!}</div>
        <div class="fb-like" data-href="{{ request()->url() }}" data-layout="standard" data-action="like"
            data-show-faces="false" data-share="true"></div>
    </div>
    @php $relatedPosts = get_related_posts($post->id, 2); @endphp

    @if ($relatedPosts->isNotEmpty())
        <footer class="post__footer">
            <div class="row">
                @foreach ($relatedPosts as $relatedItem)
                    <div class="col-md-6 col-sm-6 col-12">
                        <div
                            class="post__relate-group @if ($loop->last) post__relate-group--right text-end @else text-start @endif">
                            <h4 class="relate__title">
                                @if ($loop->first)
                                    {{ __('Previous Post') }}
                                @else
                                    {{ __('Next Post') }}
                                @endif
                            </h4>
                            <article class="post post--related">
                                <div class="post__thumbnail"><a href="{{ $relatedItem->url }}"
                                        title="{{ $relatedItem->name }}" class="post__overlay"></a>
                                    {{ RvMedia::image($relatedItem->image, $relatedItem->name, 'thumb') }}
                                </div>
                                <header class="post__header">
                                    <p><a href="{{ $relatedItem->url }}" class="post__title">
                                            {{ $relatedItem->name }}</a></p>
                                    <div class="post__meta"><span
                                            class="post__created-at">{{ Theme::formatDate($post->created_at) }}</span>
                                    </div>
                                </header>
                            </article>
                        </div>
                    </div>
                @endforeach
            </div>
        </footer>
    @endif
    <br>
    {!! apply_filters(BASE_FILTER_PUBLIC_COMMENT_AREA, null, $post) !!}
</article>
