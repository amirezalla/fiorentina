{!! Theme::partial('header') !!}
@if (Theme::get('section-name'))
    {!! Theme::partial('breadcrumbs') !!}
@endif

@include('ads.includes.background-page')

@if (Theme::get('isArticle'))
    <div class="background-ad-post">

    </div>
    <div class="container mb-3">
        <div class="col-11 row justify-content-center mx-auto" style="padding: 0">
            <div class="d-none d-md-block col-6 mx-auto" style="padding: 4px;padding-left:0">
                <div class="col-12">
                    @include('ads.includes.SIZE_468X60_TOP_SX')
                </div>
            </div>
            <div class="d-none d-md-block col-6 mx-auto" style="padding: 0">
                <div class="col-12">
                    @include('ads.includes.SIZE_468X60_TOP_DX')
                </div>
            </div>
            <div class="d-block d-md-none col-12 text-center p-0">
                @include('ads.includes.MOBILE_HOME_TOP_24')
            </div>
        </div>
    </div>
@endif

<section class="section pt-50 pb-50">

    <div class="container mobile-p-0" style="position: relative;margin-top: -66px;">
        <div class="row row col-lg-11 bg-white mx-auto pb-50">

            <div class="col-lg-8">
                <div class="page-content">
                    {!! Theme::content() !!}
                </div>
            </div>
            @if (Request::path() !== 'diretta')
                {{-- Check if the page is not /diretta --}}
                <div class="col-lg-4">
                    {!! Theme::partial('sidebar') !!}
                    @if (Request::path() == 'calendario-primavera')
                        @include('ads.includes.primaverastanding')
                    @endif
                </div>
            @elseif (Request::path() == 'diretta')
                @include('ads.includes.livechat')
            @endif

        </div>
</section>
{!! Theme::partial('footer') !!}
