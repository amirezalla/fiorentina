<section class="section page-intro pt-50 pb-50 bg-cover">
    <div style="opacity: 0.6" class="bg-overlay"></div>
    <div class="container">
        <h3 class="page-intro__title">{{ Theme::get('section-name') ?: SeoHelper::getTitle() }}</h3>
        {!! Theme::breadcrumb()->render() !!}
    </div>
</section>
