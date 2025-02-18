{!! Theme::partial('header') !!}
@if (Theme::get('section-name'))
    {!! Theme::partial('breadcrumbs') !!}
@endif
<section class="section pt-50 pb-100">
    @if (Theme::get('has-ads-background'))
        {!! BaseHelper::clean(Theme::get('has-ads-background')) !!}
    @endif

    <div class="container bg-white" style="margin-top: -66px;">
        <div class="row">
            <div class="col-lg-8">
                <div class="page-content">
                    {!! Theme::content() !!}
                </div>
            </div>
            @if (Request::path() !== 'diretta')
                {{-- Check if the page is not /diretta --}}
                <div class="col-lg-4">
                    {!! Theme::partial('sidebar') !!}
                </div>
            @elseif (Request::path() == 'diretta')
                @include('ads.includes.livechat')

            @elseif (Request::path() == 'calendario-primavera')
                @include('ads.includes.primaverastanding')
            @endif
        </div>
</section>
{!! Theme::partial('footer') !!}
