<?php
namespace OrangeShadow\Polls;

use OrangeShadow\Polls\PollProxy;

class ServiceProvider extends \Illuminate\Support\ServiceProvider
{
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind('PollProxy', PollProxy::class );
    }


    public function boot()
    {

        $this->publishes([
            __DIR__.'/config/polls.php' => config_path('polls.php'),
        ],'config');

        $this->loadMigrationsFrom(__DIR__.'/migrations');

        $this->loadRoutesFrom(__DIR__.'/Http/routes.php');

    }
}
