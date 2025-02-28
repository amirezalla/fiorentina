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
        $this->app->extend('url', function (UrlGenerator $url, $app) {
            $routes = $app['router']->getRoutes();
            $customUrl = new CustomUrlGenerator($routes, $app->make('request'));

            // Copy the root controller namespace.
            $customUrl->setRootControllerNamespace($url->getRootControllerNamespace());
            
            // Set the forced root URL based on the current request.
            $customUrl->forceRootUrl($url->formatRoot($url->getRequest()));

            // If a forced root URL is explicitly set, use it.
            if ($url->getRootUrl()) {
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
        // No additional registration needed.
    }
}
