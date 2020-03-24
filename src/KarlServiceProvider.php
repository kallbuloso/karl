<?php

namespace kallbuloso\Karl;

use Illuminate\Support\ServiceProvider;

class KarlServiceProvider extends ServiceProvider
{
    /**
     * The console commands.
     *
     * @var bool
     */
    protected $commands = [
        'kallbuloso\Karl\Commands\Auth\AuthMakeCommand',
        'kallbuloso\Karl\Commands\Auth\ConfirmMakeCommand',
        'kallbuloso\Karl\Commands\MultiAuth\MultiAuthCommand',
        'kallbuloso\Karl\Commands\Schema\MakeSchemaCommand',
        'kallbuloso\Karl\Commands\ResetDB\MakeResetDBCommand',
        'kallbuloso\Karl\Commands\ModelReplaces\ModelReplaceCommand',
        'kallbuloso\Karl\Commands\ModelReplaces\ModelsDefaulCommnand',
        'kallbuloso\Karl\Commands\ModelReplaces\ModelsRewriteCommand',
    ];
    /**
     * Perform post-registration booting of services.
     *
     * @return void
     */
    public function boot()
    {
        // $this->loadTranslationsFrom(__DIR__.'/../resources/lang', 'kallbuloso');
        // $this->loadViewsFrom(__DIR__.'/../resources/views', 'kallbuloso');
        // $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
        // $this->loadRoutesFrom(__DIR__.'/routes.php');

        // Publishing is only necessary when using the CLI.
        if ($this->app->runningInConsole()) {
            $this->bootForConsole();
        }
    }

    /**
     * Register any package services.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(__DIR__.'/../config/karl.php', 'karl');

        // Register the service the package provides.
        $this->app->singleton('karl', function ($app) {
            return new Karl;
        });

        $this->commands($this->commands);
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return $this->commands;
    }

    /**
     * Console-specific booting.
     *
     * @return void
     */
    protected function bootForConsole()
    {
        // Publishing the configuration file.
        $this->publishes([
            __DIR__.'/../config/karl.php' => config_path('karl.php'),
        ], 'karl.config');

        // Publishing the views.
        /*$this->publishes([
            __DIR__.'/../resources/views' => base_path('resources/views/vendor/kallbuloso'),
        ], 'karl.views');*/

        // Publishing assets.
        /*$this->publishes([
            __DIR__.'/../resources/assets' => public_path('vendor/kallbuloso'),
        ], 'karl.views');*/

        // Publishing the translation files.
        /*$this->publishes([
            __DIR__.'/../resources/lang' => resource_path('lang/vendor/kallbuloso'),
        ], 'karl.views');*/

        // Registering package commands.
        // $this->commands([]);
    }
}
