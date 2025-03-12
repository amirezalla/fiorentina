<!DOCTYPE html>
<html {!! Theme::htmlAttributes() !!}>

<head>
    <meta http-equiv="Content-Security-Policy" content="upgrade-insecure-requests">
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta content="width=device-width, initial-scale=1, minimum-scale=1, maximum-scale=5, user-scalable=1"
        name="viewport" />
    <meta name="format-detection" content="telephone=no">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <noscript>
        <style amp-boilerplate>
            body {
                -webkit-animation: none;
                -moz-animation: none;
                -ms-animation: none;
                animation: none
            }
        </style>
    </noscript>
    <script data-ad-client="ca-pub-6741446998584415" async
        src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script>
    <script async src="https://cdn.ampproject.org/v0.js"></script>
    <script async custom-element="amp-ad" src="https://cdn.ampproject.org/v0/amp-ad-0.1.js"></script>
    @php(Theme::set('headerMeta', Theme::partial('header-meta')))

    {!! Theme::header() !!}
</head>

<body {!! Theme::bodyAttributes() !!}>
    {!! apply_filters(THEME_FRONT_BODY, null) !!}
    <header data-sticky="false" data-sticky-checkpoint="200" data-responsive="991"
        class="page-header page-header--light py-0">
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


    <header class="page-header page-header--light py-0" data-sticky="false" data-sticky-checkpoint="200"
        data-responsive="991">
        <div class="container">
            <div class="row">
                <!-- Mobile Header -->
                <div class="col-12 d-lg-none">
                    <div class="mobile-header d-flex justify-content-between align-items-center py-2">
                        <!-- Optional logo -->
                        <div class="mobile-logo">
                            <a href="{{ url('/') }}">Logo</a>
                        </div>
                        <div class="mobile-member">
                            @if (is_plugin_active('member'))
                                @if (auth('member')->check())
                                    <a href="{{ route('public.member.dashboard') }}" class="d-flex align-items-center">
                                        <img src="{{ auth('member')->user()->avatar_thumb_url }}"
                                            alt="{{ auth('member')->user()->name }}" width="20" class="img-circle"
                                            loading="lazy">
                                        <span class="ml-2">{{ auth('member')->user()->name }}</span>
                                    </a>
                                    <a href="#"
                                        onclick="event.preventDefault(); document.getElementById('logout-form').submit();"
                                        class="d-flex align-items-center ml-3">
                                        {!! BaseHelper::renderIcon('ti ti-login-2') !!} {{ __('Logout') }}
                                    </a>
                                @else
                                    <a href="{{ route('public.member.login') }}" class="d-flex align-items-center">
                                        {!! BaseHelper::renderIcon('ti ti-login-2') !!} {{ __('ACCEDI') }}
                                    </a>
                                @endif
                            @endif
                        </div>
                        <!-- Mobile Menu Toggle -->
                        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#mobileMenu"
                            aria-controls="mobileMenu" aria-expanded="false" aria-label="Toggle navigation">
                            <span class="navbar-toggler-icon"></span>
                        </button>
                    </div>
                    <!-- Mobile Navigation Menu -->
                    <div class="collapse navbar-collapse" id="mobileMenu">
                        {!! Menu::renderMenuLocation('main-menu', [
                            'options' => ['class' => 'menu sub-menu--slideLeft'],
                            'view' => 'main-menu',
                        ]) !!}
                    </div>
                </div>

                <!-- Desktop Header -->
                <div class="col-12 d-none d-lg-flex justify-content-between align-items-center py-2">
                    <nav class="desktop-navigation">
                        {!! Menu::renderMenuLocation('main-menu', [
                            'options' => ['class' => 'menu sub-menu--slideLeft'],
                            'view' => 'main-menu',
                        ]) !!}
                    </nav>
                    <div class="desktop-member d-flex align-items-center">
                        @if (is_plugin_active('member'))
                            @if (auth('member')->check())
                                <a href="{{ route('public.member.dashboard') }}" class="d-flex align-items-center">
                                    <img src="{{ auth('member')->user()->avatar_thumb_url }}"
                                        alt="{{ auth('member')->user()->name }}" width="20" class="img-circle"
                                        loading="lazy">
                                    <span class="ml-2">{{ auth('member')->user()->name }}</span>
                                </a>
                                <a href="#"
                                    onclick="event.preventDefault(); document.getElementById('logout-form').submit();"
                                    class="d-flex align-items-center ml-3">
                                    {!! BaseHelper::renderIcon('ti ti-login-2') !!} {{ __('Logout') }}
                                </a>
                            @else
                                <a href="{{ route('public.member.login') }}" class="d-flex align-items-center">
                                    {!! BaseHelper::renderIcon('ti ti-login-2') !!} {{ __('ACCEDI') }}
                                </a>
                            @endif
                        @endif
                    </div>
                </div>
            </div>
        </div>

        @if (auth('member')->check())
            <form id="logout-form" action="{{ route('public.member.logout') }}" method="POST" style="display: none;">
                @csrf
            </form>
        @endif

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
