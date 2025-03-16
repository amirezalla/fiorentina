@php
    use App\Http\Controllers\WpImportController;
    Theme::set('section-name', $post->name);
    $post->loadMissing('metadata');

    if ($bannerImage = $post->getMetaData('banner_image', true)) {
        Theme::set('breadcrumbBannerImage', RvMedia::getImageUrl($bannerImage));
    }
    $content = \App\Models\Ad::addAdsToContent($post->content);
    dd($post->comments);
    if (!$post->comments) {
        WpImportController::importComment($post->id);
    }
@endphp

<article class="post post--single">
    <header class="post__header" style="padding-top: 20px">
        @if ($post->first_category?->name)
            <span class="post-category"
                style="display: block;
            width: fit-content;
            margin-bottom: 10px;">
                <a class="category-label" href="{{ $post->first_category->url }}">{{ $post->first_category->name }}</a>
            </span>
        @endif

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

    <br>
    {!! apply_filters(BASE_FILTER_PUBLIC_COMMENT_AREA, null, $post) !!}
</article>
