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
            
            // Set the forced root URL using the request's root.
            $customUrl->forceRootUrl($app['request']->root());
            
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
