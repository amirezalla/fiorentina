<!DOCTYPE html>
<html {!! Theme::htmlAttributes() !!}>

<head>
    <meta http-equiv="Content-Security-Policy" content="upgrade-insecure-requests">
    <meta charset="utf-8">
    <link href="https://fonts.googleapis.com/css2?family=Roboto+Condensed&display=swap" rel="stylesheet">

    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta content="width=device-width, initial-scale=1, minimum-scale=1, maximum-scale=5, user-scalable=1"
        name="viewport" />
    <meta name="format-detection" content="telephone=no">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <script src="/jaqisharam.js"></script>
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
    {{-- <title>{{ Theme::getTitle() }}</title> --}}
</head>

<body {!! Theme::bodyAttributes() !!}>
    {!! apply_filters(THEME_FRONT_BODY, null) !!}

    <header data-sticky="false" data-sticky-checkpoint="200" data-responsive="991"
        class="page-header page-header--light py-0">
        <div class="container headup"
            style="display: flex; justify-content: space-between; align-items: center;max-width: 1200px; margin: auto;">
            <div class="page-header__left">
                <a href="{{ BaseHelper::getHomepageUrl() }}" class="page-logo">
                    {{ Theme::getLogoImage() }}
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

    <header data-sticky="false" data-sticky-checkpoint="200" data-responsive="991"
        class="page-header page-header--light py-0">
        <div class="container d-flex" style="max-width: 1200px">
            <div class="page-header__right flex-grow-1"
                style="border-top: 1px solid rgba(0,0,0,0.2); padding-top: 10px;">
                <!-- Mobile Navigation Toggle Button -->
                <div id="nav-toggle" class="navigation-toggle"><span></span></div>
                <div>
                    <ul class="d-flex align-items-center" style="list-style: none; margin: 0; padding: 0;">
                        @if (is_plugin_active('member'))
                            @if (auth('member')->check())
                                <li class="d-lg-none d-md-block d-sm-block" style="margin-left: 20px;">
                                    <a href="{{ route('public.member.dashboard') }}" rel="nofollow"
                                        style="display: flex; align-items: center;">
                                        <img src="{{ auth('member')->user()->avatar_thumb_url }}" class="img-circle"
                                            width="20" alt="{{ auth('member')->user()->name }}" loading="lazy">
                                        &nbsp;<span>{{ auth('member')->user()->name }}</span>
                                    </a>
                                </li>
                                <li class="d-lg-none d-md-block d-sm-block" style="margin-left: 20px;">
                                    <a href="#"
                                        onclick="event.preventDefault(); document.getElementById('logout-form').submit();"
                                        rel="nofollow" style="display: flex; align-items: center;">
                                        {!! BaseHelper::renderIcon('ti ti-login-2') !!} {{ __('Logout') }}
                                    </a>
                                </li>
                            @else
                                <li class="d-lg-none d-md-block d-sm-block" style="margin-left: 20px;">
                                    <a href="{{ route('public.member.login') }}" rel="nofollow" class="accedi-header">
                                        {!! BaseHelper::renderIcon('ti ti-login-2') !!} {{ __('ACCEDI') }}
                                    </a>
                                </li>
                            @endif
                        @endif
                    </ul>
                </div>
                <div class="float-start w-100"
                    style="display: flex; align-items: center; justify-content: space-between;">
                    <nav class="d-flex navigation navigation--light navigation--fadeRight">
                        {!! Menu::renderMenuLocation('main-menu', [
                            'options' => ['class' => 'menu sub-menu--slideLeft'],
                            'view' => 'main-menu',
                        ]) !!}
                        <ul class="d-flex align-items-center" style="list-style: none; margin: 0; padding: 0;">
                            @if (is_plugin_active('member'))
                                @if (auth('member')->check())
                                    <li class="d-lg-block d-none" style="margin-left: 20px;">
                                        <a href="{{ route('public.member.dashboard') }}" rel="nofollow"
                                            style="display: flex; align-items: center;">
                                            <img src="{{ auth('member')->user()->avatar_thumb_url }}"
                                                class="img-circle" width="20"
                                                alt="{{ auth('member')->user()->name }}" loading="lazy">
                                            &nbsp;<span>{{ auth('member')->user()->name }}</span>
                                        </a>
                                    </li>
                                    <li class="d-lg-block d-none" style="margin-left: 20px;">
                                        <a href="#"
                                            onclick="event.preventDefault(); document.getElementById('logout-form').submit();"
                                            rel="nofollow" style="display: flex; align-items: center;">
                                            {!! BaseHelper::renderIcon('ti ti-login-2') !!} {{ __('Logout') }}
                                        </a>
                                    </li>
                                @else
                                    <li class="d-lg-block d-none" style="margin-left: 20px;">
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

    <!-- Mobile Fullscreen Menu -->
    <div id="mobile-menu" class="mobile-menu">
        <div class="mobile-menu-header">
            <!-- Logo on the left -->
            <div class="mobile-menu-logo">
                <a href="{{ BaseHelper::getHomepageUrl() }}" class="page-logo">
                    {{ Theme::getLogoImage() }}
                </a>
            </div>

            <!-- Close button on the right -->
            <span id="close-menu" class="close-menu">&times;</span>
        </div>

        <nav class="mobile-menu-content">
            {!! Menu::renderMenuLocation('main-menu', [
                'options' => ['class' => 'menu mobile-menu-list'],
                'view' => 'main-menu',
            ]) !!}
        </nav>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            // 1. Toggle mobile menu open/close
            var navToggle = document.getElementById('nav-toggle');
            var closeMenu = document.getElementById('close-menu');
            var mobileMenu = document.getElementById('mobile-menu');

            if (navToggle && closeMenu && mobileMenu) {
                navToggle.addEventListener('click', function() {
                    mobileMenu.classList.add('open');
                });
                closeMenu.addEventListener('click', function() {
                    mobileMenu.classList.remove('open');
                });
            }

            // 2. For each parent item with a submenu, add the “+” opener if not present
            //    and allow clicking either the parent link or the plus sign to toggle.
            var submenuItems = document.querySelectorAll('#mobile-menu .menu li.menu-item-has-children');

            submenuItems.forEach(function(item) {
                var opener = item.querySelector('.submenu-opener');
                var anchor = item.querySelector('a');

                // Create the .submenu-opener if it doesn’t exist
                if (!opener) {
                    opener = document.createElement('span');
                    opener.classList.add('submenu-opener');
                    opener.innerHTML = '+';
                    if (anchor) {
                        anchor.parentNode.insertBefore(opener, anchor.nextSibling);
                    } else {
                        item.appendChild(opener);
                    }
                }

                // Toggle function for submenu
                function toggleSubmenu(e) {
                    e.preventDefault(); // Don’t navigate
                    e.stopPropagation(); // Don’t bubble up
                    item.classList.toggle('open');
                    opener.innerHTML = item.classList.contains('open') ? '-' : '+';
                }

                // Let the entire parent link open/close the submenu
                if (anchor) {
                    anchor.addEventListener('click', toggleSubmenu);
                }

                // Plus sign also opens/closes the submenu
                opener.addEventListener('click', toggleSubmenu);
            });
        });
    </script>
