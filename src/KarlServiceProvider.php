<?php

namespace kallbuloso\Karl;

use Illuminate\Support\ServiceProvider;
use kallbuloso\Karl\Builder\LaraCrud\DbReader\Database;

class KarlServiceProvider extends ServiceProvider
{
    protected $defer = true;

    /**
     * List of command which will be registered.
     * @var array
     */
    protected $commands = [
        'kallbuloso\Karl\Commands\LaraCrud\Controller',
        'kallbuloso\Karl\Commands\LaraCrud\Factory',
        'kallbuloso\Karl\Commands\LaraCrud\Migration',
        'kallbuloso\Karl\Commands\LaraCrud\Model',
        'kallbuloso\Karl\Commands\LaraCrud\Mvc',
        'kallbuloso\Karl\Commands\LaraCrud\Package',
        'kallbuloso\Karl\Commands\LaraCrud\Policy',
        'kallbuloso\Karl\Commands\LaraCrud\Request',
        'kallbuloso\Karl\Commands\LaraCrud\Route',
        'kallbuloso\Karl\Commands\LaraCrud\Test',
        'kallbuloso\Karl\Commands\LaraCrud\Transformer',
        'kallbuloso\Karl\Commands\LaraCrud\View',
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
            //DbReader\Database settings
            Database::settings([
                'pdo' => app('db')->connection()->getPdo(),
                'manualRelations' => config('laracrud.model.relations', []),
                'ignore' => config('laracrud.view.ignore', []),
                'protectedColumns' => config('laracrud.model.protectedColumns', []),
                'files' => config('laracrud.image.columns', []),
            ]);
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

        // Publish Templates to view/vendor folder so user can customize this own templates
        $this->publishes([
            __DIR__ . '/Builder/LaraCrud/resources/templates' => resource_path('views/vendor/laracrud/templates')
        ], 'laracrud-template');

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
