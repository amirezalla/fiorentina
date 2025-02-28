<?php

namespace App\Providers;

use Illuminate\Routing\UrlGenerator;
use App\Routing\CustomUrlGenerator;
use Illuminate\Support\ServiceProvider;

class CustomUrlGeneratorServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap services.
     */
    public function boot()
    {
        // Replace the default UrlGenerator with our custom version.
        $this->app->extend('url', function (UrlGenerator $url, $app) {
            $routes = $app['router']->getRoutes();
            // Instantiate our custom URL generator
            $customUrl = new CustomUrlGenerator($routes, $app->make('request'));

            // Copy any settings from the original URL generator if needed.
            $customUrl->setRootControllerNamespace($url->getRootControllerNamespace());
            $customUrl->forceRootUrl($url->formatRoot($url->getRequest()));
            if ($url->isForcedRootUrl()) {
                $customUrl->forceRootUrl($url->getRootUrl());
            }

            return $customUrl;
        });
    }

    /**
     * Register services.
     */
    public function register()
    {
        // No registration needed here
    }
}
