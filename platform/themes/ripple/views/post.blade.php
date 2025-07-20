@php
    use App\Http\Controllers\WpImportController;

    // ❶ Page-level data -------------------------------------------------------
    Theme::set('section-name', $post->name);
    $post->loadMissing('metadata');

    if ($bannerImage = $post->getMetaData('banner_image', true)) {
        Theme::set('breadcrumbBannerImage', RvMedia::getImageUrl($bannerImage));
    }

    $content = \App\Models\Ad::addAdsToContent($post->content);

    // ❷ Import WP comments on first view -------------------------------------
    $comments = FriendsOfBotble\Comment\Models\Comment::where('reference_id', $post->id)->get();
    if ($comments->isEmpty()) {
        WpImportController::importComment($post->id);
    }

    // ❸ Let the layout know this is a full-width article ---------------------
    Theme::set('isArticle', true);
@endphp


<article class="post post--single">

    {{-- ============ Header ============ --}}
    <header class="post__header" style="padding-top:20px">

        {{-- Category badge ----------------------------------------------------- --}}
        @if ($post->first_category?->name)
            <span class="post-category post-group__left-purple-badge"
                style="display:block;width:fit-content;margin-bottom:10px;">
                <a class="category-label" style="font-size:14px!important" href="{{ $post->first_category->url }}">
                    {{ $post->first_category->name }}
                </a>
            </span>
        @endif

        {{-- Title -------------------------------------------------------------- --}}
        <h1 class="post__title post__title_in">{{ $post->name }}</h1>

        {{-- Meta data (wrapped so it lines up nicely) -------------------------- --}}
        <div class="col-8">
            <div class="post__meta">
                {!! Theme::partial('blog.post-meta', compact('post')) !!}

                @if ($post->tags->isNotEmpty())
                    @php
                        if (is_plugin_active('language') && is_plugin_active('language-advanced')) {
                            $post->tags->loadMissing('translations');
                        }
                    @endphp
                @endif
            </div> {{-- /.post__meta --}}
        </div> {{-- /.col-8 --}}

    </header>


    {{-- ============ Content ============ --}}
    <div class="post__content">

        {{-- Image gallery (if any) --------------------------------------------- --}}
        @if (defined('GALLERY_MODULE_SCREEN_NAME') && !empty(($galleries = gallery_meta_data($post))))
            {!! render_object_gallery($galleries, $post->first_category ? $post->first_category->name : __('Uncategorized')) !!}
        @endif

        {{-- Main body ---------------------------------------------------------- --}}
        <div class="ck-content amir" style="color:black">
            {!! BaseHelper::clean($content) !!}
        </div>

        {{-- Facebook like/share ------------------------------------------------- --}}
        <div class="fb-like" data-href="{{ request()->url() }}" data-layout="standard" data-action="like"
            data-show-faces="false" data-share="true">
        </div>
    </div> {{-- /.post__content --}}


    {{-- ============ Comments ============ --}}
    <br>
    {!! apply_filters(BASE_FILTER_PUBLIC_COMMENT_AREA, null, $post) !!}


    {{-- ============ Sidebar (only relevant if the layout still shows it) ===== --}}
    <div class="col-lg-4">
        {!! Theme::partial('sidebar') !!}
    </div>

</article>
