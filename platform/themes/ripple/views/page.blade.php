@php
    $adsBackgroundShortCodeRegex = '/<shortcode>\[ads-background.*?\](.*?)\[\/ads-background.*?\]<\/shortcode>/';
    $content = preg_replace($adsBackgroundShortCodeRegex, '', $page->content);
@endphp
@if (preg_match($adsBackgroundShortCodeRegex, $page->content, $match) && count($match))
    @php
        Theme::set('has-ads-background', $match[0]);
    @endphp
@endif


@if (!BaseHelper::isHomepage($page->id))
    @php
        Theme::set('section-name', SeoHelper::getTitle());
        $page->loadMissing('metadata');

        $bannerImage = $page->getMetaData('banner_image', true);

        if ($bannerImage) {
            Theme::set('breadcrumbBannerImage', RvMedia::getImageUrl($bannerImage));
        }

    @endphp
    <article class="post post--single">
        <div class="post__content recent-posts-container">
            <div class="col-md-12 col-sm-12 col-12">
                @if (defined('GALLERY_MODULE_SCREEN_NAME') && !empty(($galleries = gallery_meta_data($page))))
                    {!! render_object_gallery($galleries) !!}
                @endif
                {!! apply_filters(
                    PAGE_FILTER_FRONT_PAGE_CONTENT,
                    Html::tag('div', BaseHelper::clean($content), ['class' => 'ck-content'])->toHtml(),
                    $page,
                ) !!}
            </div>
        </div>
    </article>
@else
    @if (defined('GALLERY_MODULE_SCREEN_NAME') && !empty(($galleries = gallery_meta_data($page))))
        {!! render_object_gallery($galleries) !!}
    @endif
    {!! apply_filters(
        PAGE_FILTER_FRONT_PAGE_CONTENT,
        Html::tag('div', BaseHelper::clean($content), ['class' => 'ck-content'])->toHtml(),
        $page,
    ) !!}
@endif
