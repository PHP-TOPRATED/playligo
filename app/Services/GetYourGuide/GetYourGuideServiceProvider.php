<?php

namespace App\Services\GetYourGuide;


use Illuminate\Support\ServiceProvider;

class GetYourGuideServiceProvider extends ServiceProvider
{
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;

    /**
     * Bootstrap the application events.
     *
     * @return void
     */
    public function boot()
    {
        $loader = \Illuminate\Foundation\AliasLoader::getInstance();
        $loader->alias('GetYourGuide', 'App\Services\GetYourGuide\Facade\GetYourGuide');
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind("getyourguide", function () {
            return $this->app->make('App\Services\GetYourGuide\GetYourGuide', [config('getyourguide.api_key')]);
        });
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return array('getyourguide');
    }
}