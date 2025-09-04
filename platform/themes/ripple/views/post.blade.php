@php
    use App\Http\Controllers\WpImportController;

    // ❶ Page-level data -------------------------------------------------------
    Theme::set('section-name', $post->name);
    $post->loadMissing('metadata');

    if ($bannerImage = $post->getMetaData('banner_image', true)) {
        Theme::set('breadcrumbBannerImage', RvMedia::getImageUrl($bannerImage));
    }

    $html = (string) $post->content;
    $wrapped = \App\Models\Ad::wrapInlineIntoParagraphs($html);
    $splitted = \App\Models\Ad::splitLongParagraphs($wrapped); // 5-rows rule
    $content = \App\Models\Ad::addAdsToContent($splitted);

    // ❷ Import WP comments on first view -------------------------------------
    $comments = FriendsOfBotble\Comment\Models\Comment::where('reference_id', $post->id)->get();
    if ($comments->isEmpty()) {
        WpImportController::importComment($post->id);
    }

    // ❸ Let the layout know this is a full-width article ---------------------
    Theme::set('isArticle', true);
@endphp


<article class="post post--single">
    <div class="row">



        {{-- ============ Header ============ --}}
        <header class="post__header mb-3" style="padding-top:20px">

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
        </header>

        {{-- Meta data (wrapped so it lines up nicely) -------------------------- --}}
        <div class="col-lg-8">
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
            {{-- /.col-8 --}}




            {{-- ============ Content ============ --}}
            <div class="post__content">

                {{-- Image gallery (if any) --------------------------------------------- --}}
                @if (defined('GALLERY_MODULE_SCREEN_NAME') && !empty(($galleries = gallery_meta_data($post))))
                    {!! render_object_gallery($galleries, $post->first_category ? $post->first_category->name : __('Uncategorized')) !!}
                @endif

                {{-- Main body ---------------------------------------------------------- --}}
                <div class="ck-content amir" style="color:black">
                    {!! BaseHelper::clean($html) !!}
                </div>

                {{-- Facebook like/share ------------------------------------------------- --}}
                <div class="fb-like" data-href="{{ request()->url() }}" data-layout="standard" data-action="like"
                    data-show-faces="false" data-share="true">
                </div>
            </div> {{-- /.post__content --}}


            {{-- ============ Comments ============ --}}
            <br>
            {!! apply_filters(BASE_FILTER_PUBLIC_COMMENT_AREA, null, $post) !!}






        </div>

        {{-- ============ Sidebar (only relevant if the layout still shows it) ===== --}}
        <div class="col-lg-4">
            {!! Theme::partial('sidebar') !!}
        </div>




        {{-- ============================================================
|  Related posts: last 4 from the same category
|  Insert right after the comments section in post.blade.php
|============================================================ --}}
        @php
            use Illuminate\Support\Str;
            use Botble\Blog\Models\Post;

            $relatedPosts = collect();

            if ($post->first_category) {
                $relatedPosts = Post::with(['slugable', 'metadata'])
                    ->whereHas('categories', fn($q) => $q->where('categories.id', $post->first_category->id))
                    ->where('id', '!=', $post->id) // exclude this post
                    ->published() // ← now returns rows
                    ->latest()
                    ->take(4)
                    ->get();
            }
        @endphp




        @if ($relatedPosts->isNotEmpty())
            <div class="related-posts mt-5 col-lg-12">

                {{-- ── section heading ─────────────────────────────────────────────── --}}
                <h3 class="fw-bold text-uppercase mb-3"
                    style="font-family:'Titillium Web';font-size:1rem;border-bottom:2px solid #ccc;padding-bottom:4px;">
                    <span style="border-bottom:2px solid #8424e3;padding-bottom:4px;">
                        ALTRE NOTIZIE {{ $post->first_category->name }}
                    </span>
                </h3>

                {{-- ── grid of four items ──────────────────────────────────────────── --}}
                <div class="row gx-3 gy-4">
                    @foreach ($relatedPosts as $item)
                        <div class="col-12 col-sm-6 col-md-3">
                            <article>

                                {{-- thumbnail --}}
                                <a href="{{ $item->url }}" class="d-block mb-2">
                                    <img src="{{ RvMedia::getImageUrl($item->image, 'medium', false, RvMedia::getDefaultImage()) }}"
                                        alt="{{ $item->name }}" class="img-fluid w-100">
                                </a>

                                {{-- title --}}
                                <h4 class="h6 fw-bold mb-1" style="font-family:'Titillium Web';">
                                    <a href="{{ $item->url }}" class="text-dark text-decoration-none"
                                        style="font-size: 1.2rem;font-weight: 600;font-family: 'Titillium Web';line-height:1.2;">
                                        {{-- Limit title length to 90 characters --}}
                                        {{ Str::limit($item->name, 90) }}
                                    </a>
                                </h4>

                                {{-- short excerpt (optional) --}}
                                <p class="small mb-0"
                                    style="    color: #555;font-family: 'Titillium Web';
    font-size: 1rem;
    font-weight: 400;
    line-height: 1.3;
    margin-top: 7px;
    width: 100%;">
                                    {{ Str::limit(strip_tags($item->description ?: $item->content), 90) }}
                                </p>

                            </article>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

    </div>



</article>
