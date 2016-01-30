<?php namespace Fish\Logger;

use Illuminate\Support\ServiceProvider;

/**
 * Class SluggableServiceProvider
 *
 * @package Cviebrock\EloquentSluggable
 */
class LoggerServiceProvider extends ServiceProvider
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
        $this->registerCommands();
    }

    /**
     * Register the artisan commands
     *
     * @return void
     */
    public function registerCommands()
    {
        $this->app['logger.init'] = $this->app->share(function ($app) {

            return new InitLoggerCommand();
        });

        $this->commands('logger.init');
    }

}
