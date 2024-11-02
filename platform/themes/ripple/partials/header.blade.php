<!DOCTYPE html>
<html {!! Theme::htmlAttributes() !!}>

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta content="width=device-width, initial-scale=1, minimum-scale=1, maximum-scale=5, user-scalable=1"
        name="viewport" />
    <meta name="format-detection" content="telephone=no">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <noscript>
        <style amp-boilerplate>
          body{
            -webkit-animation:none;
            -moz-animation:none;
            -ms-animation:none;
            animation:none
          }
        </style>
      </noscript>
      <script async src="https://cdn.ampproject.org/v0.js"></script>
      <script async custom-element="amp-ad" src="https://cdn.ampproject.org/v0/amp-ad-0.1.js"></script>
    @php(Theme::set('headerMeta', Theme::partial('header-meta')))

    {!! Theme::header() !!}
</head>

<body {!! Theme::bodyAttributes() !!}>
    {!! apply_filters(THEME_FRONT_BODY, null) !!}
    <header data-sticky="false" data-sticky-checkpoint="200" data-responsive="991"
        class="page-header page-header--light py-0 z-1">
        <div class="container headup" style="display: flex; justify-content: space-between; align-items: center;">
            <div class="page-header__left">
                <a href="{{ BaseHelper::getHomepageUrl() }}" class="page-logo">
                    {{ Theme::getLogoImage() }} <!-- Increased height from 50 to 80 -->
                </a>
            </div>
            <div class="page-header__right" style="display: flex; align-items: center;">
                @if ($socialLinks = Theme::getSocialLinks())
                    <ul class="social social--simple"
                        style="display: flex; margin-right: 15px; list-style: none; padding: 0;">
                        @foreach ($socialLinks as $socialLink)
                            @continue(!($icon = $socialLink->getIconHtml()))

                            <li style="margin-right: 10px;">
                                <a {{ $socialLink->getAttributes() }}>
                                    {{ $icon }}
                                </a>
                            </li>
                        @endforeach
                    </ul>
                @endif

                <div class="search-btn c-search-toggler" style="cursor: pointer;">
                    {!! BaseHelper::renderIcon('ti ti-search', attributes: ['class' => 'close-search']) !!}
                </div>
            </div>
        </div>
    </header>

    {{--    <header class="header" id="header"> --}}
    {{--        <div class="header-wrap d-none d-sm-block h-34px"> --}}

    {{--            <nav class="nav-top"> --}}
    {{--                <div class="container"> --}}
    {{--                    <div class="row"> --}}

    {{--                        @if ($socialLinks = Theme::getSocialLinks()) --}}


    {{--                            <div class="col-sm-4 d-flex align-items-center h-34px"> --}}
    {{--                                <ul class="social social--simple"> --}}
    {{--                                    @foreach ($socialLinks as $socialLink) --}}
    {{--                                        @continue(!($icon = $socialLink->getIconHtml())) --}}

    {{--                                        <li> --}}
    {{--                                            <a {{ $socialLink->getAttributes() }}> --}}
    {{--                                                {{ $icon }} --}}
    {{--                                            </a> --}}
    {{--                                        </li> --}}

    {{--                                    @endforeach --}}
    {{--                                </ul> --}}
    {{--                            </div> --}}
    {{--                        @endif --}}

    {{--                    </div> --}}
    {{--                </div> --}}
    {{--            </nav> --}}
    {{--        </div> --}}
    {{--    </header> --}}
    <header data-sticky="false" data-sticky-checkpoint="200" data-responsive="991"
        class="page-header page-header--light py-0 z-1">
        <div class="container d-flex">

            <div class="page-header__right flex-grow-1">
                <div class="navigation-toggle " style="display: none"><span></span></div>
                <div style="margin-top:17px;">

                    <ul class="d-flex align-items-center" style="list-style: none; margin: 0; padding: 0;">
                        @if (is_plugin_active('member'))
                            @if (auth('member')->check())
                                <li class=" d-lg-none d-md-block d-sm-block" style="margin-left: 20px;">
                                    <a href="{{ route('public.member.dashboard') }}" rel="nofollow"
                                        style="display: flex; align-items: center;">
                                        <img src="{{ auth('member')->user()->avatar_thumb_url }}" class="img-circle"
                                            width="20" alt="{{ auth('member')->user()->name }}" loading="lazy">
                                        &nbsp;<span>{{ auth('member')->user()->name }}</span>
                                    </a>
                                </li>
                                <li class=" d-lg-none d-md-block d-sm-block" style="margin-left: 20px;">
                                    <a href="#"
                                        onclick="event.preventDefault(); document.getElementById('logout-form').submit();"
                                        rel="nofollow" style="display: flex; align-items: center;">
                                        {!! BaseHelper::renderIcon('ti ti-login-2') !!} {{ __('Logout') }}
                                    </a>
                                </li>
                            @else
                                <li class=" d-lg-none d-md-block d-sm-block" style="margin-left: 20px;">
                                    <a href="{{ route('public.member.login') }}" rel="nofollow"
                                        style="display: flex; align-items: center;">
                                        {!! BaseHelper::renderIcon('ti ti-login-2') !!} {{ __('Login') }}
                                    </a>
                                </li>
                            @endif
                        @endif
                    </ul>


                </div>
                <div class="float-start w-100"
                    style="display: flex; align-items: center; justify-content: space-between;">
                    {{--                    <nav class="navigation navigation--light navigation--fadeRight" style="display: flex; align-items: center; flex-grow: 1; "> --}}
                    <nav class="navigation navigation--light navigation--fadeRight">


                        {!! Menu::renderMenuLocation('main-menu', [
                            'options' => ['class' => 'menu sub-menu--slideLeft'],
                            'view' => 'main-menu',
                        ]) !!}

                        <ul class="d-flex align-items-center" style="list-style: none; margin: 0; padding: 0;">
                            @if (is_plugin_active('member'))
                                @if (auth('member')->check())
                                    <li class=" d-lg-block d-none" style="margin-left: 20px;">
                                        <a href="{{ route('public.member.dashboard') }}" rel="nofollow"
                                            style="display: flex; align-items: center;">
                                            <img src="{{ auth('member')->user()->avatar_thumb_url }}"
                                                class="img-circle" width="20"
                                                alt="{{ auth('member')->user()->name }}" loading="lazy">
                                            &nbsp;<span>{{ auth('member')->user()->name }}</span>
                                        </a>
                                    </li>
                                    <li class=" d-lg-block d-none" style="margin-left: 20px;">
                                        <a href="#"
                                            onclick="event.preventDefault(); document.getElementById('logout-form').submit();"
                                            rel="nofollow" style="display: flex; align-items: center;">
                                            {!! BaseHelper::renderIcon('ti ti-login-2') !!} {{ __('Logout') }}
                                        </a>
                                    </li>
                                @else
                                    <li class=" d-lg-block d-none" style="margin-left: 20px;">
                                        <a href="{{ route('public.member.login') }}" rel="nofollow"
                                            style="display: flex; align-items: center;">
                                            {!! BaseHelper::renderIcon('ti ti-login-2') !!} {{ __('Login') }}
                                        </a>
                                    </li>
                                @endif
                            @endif
                        </ul>
                    </nav>

                    @if (auth('member')->check())
                        <form id="logout-form" action="{{ route('public.member.logout') }}" method="POST"
                            style="display: none;">
                            @csrf
                        </form>
                    @endif
                </div>

                {{--                <div class="float-start w-100"> --}}

                {{--                    <nav class="navigation navigation--light navigation--fade navigation--fadeLeft"> --}}
                {{--                        {!! Menu::renderMenuLocation('main-menu', [ --}}
                {{--                            'options' => ['class' => 'menu sub-menu--slideLeft'], --}}
                {{--                            'view' => 'main-menu', --}}
                {{--                        ]) !!} --}}







                {{--                        <li class="language-wrapper d-block d-sm-none"> --}}
                {{--                            {!! apply_filters('language_switcher') !!} --}}
                {{--                        </li> --}}
                {{--                        @if (is_plugin_active('member')) --}}
                {{--                            <ul class="d-flex"> --}}
                {{--                                @if (auth('member')->check()) --}}
                {{--                                    <li><a href="{{ route('public.member.dashboard') }}" rel="nofollow"><img --}}
                {{--                                                src="{{ auth('member')->user()->avatar_thumb_url }}" --}}
                {{--                                                class="img-circle" width="20" --}}
                {{--                                                alt="{{ auth('member')->user()->name }}" loading="lazy"> --}}
                {{--                                            &nbsp;<span>{{ auth('member')->user()->name }}</span></a></li> --}}
                {{--                                    <li><a href="#" --}}
                {{--                                           onclick="event.preventDefault(); document.getElementById('logout-form').submit();" --}}
                {{--                                           rel="nofollow">{!! BaseHelper::renderIcon('ti ti-login-2') !!} {{ __('Logout') }}</a></li> --}}
                {{--                                @else --}}
                {{--                                    <li><a href="{{ route('public.member.login') }}" --}}
                {{--                                           rel="nofollow">{!! BaseHelper::renderIcon('ti ti-login-2') !!} {{ __('Login') }}</a></li> --}}
                {{--                                @endif --}}
                {{--                            </ul> --}}
                {{--                            @if (auth('member')->check()) --}}
                {{--                                <form id="logout-form" action="{{ route('public.member.logout') }}" method="POST" --}}
                {{--                                      style="display: none;"> --}}
                {{--                                    @csrf --}}
                {{--                                </form> --}}
                {{--                            @endif --}}
                {{--                        @endif --}}

                {{--                    </nav> --}}

                {{--                </div> --}}
                <div class="clearfix"></div>
            </div>
            <div class="clearfix"></div>
        </div>
        @if (is_plugin_active('blog'))
            <div class="super-search hide" data-search-url="{{ route('public.ajax.search') }}">
                <form class="quick-search" action="{{ route('public.search') }}">
                    <input type="text" name="q" placeholder="{{ __('Type to search...') }}"
                        class="form-control search-input" autocomplete="off">
                    <span class="close-search">&times;</span>
                </form>
                <div class="search-result"></div>
            </div>
        @endif
    </header>
