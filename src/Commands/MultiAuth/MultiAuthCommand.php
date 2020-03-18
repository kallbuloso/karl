<?php

namespace kallbuloso\Karl\Commands\MultiAuth;

use kallbuloso\Karl\Helpers\ProgressBar;
use kallbuloso\Karl\Commands\BaseLocalCommand;
use kallbuloso\Karl\Builder\MultiAuth\MultiAuthTrait;

class MultiAuthCommand extends BaseLocalCommand
{
    use MultiAuthTrait;
    /**
     * The name and signature of the console command.
     * @var string
     */
    protected $signature = 'karl:make-multi-auth
                                {name=admin : Name of the guard.}
                                {--f|force : Whether to override existing files}';

    /**
     * The console command description.
     * @var string
     */
    protected $description = 'Install multi-auth package';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->usleep = '1200000';

        $this->startProgressBar(24, "Initiating...");

        $this->name = $this->argument('name');

        $this->makeProgress("Using guard: '$this->name'");

        $this->override = $this->option('force') ? true : false;

        // Check if guard is already registered
        if (array_key_exists(str_singular(snake_case($this->name)), config('auth.guards'))) {

            // Guard exists
            $this->exits = true;

            if (!$this->option('force')) {
                $this->info("Guard: '" . $this->name . "' is already registered");
                if (!$this->confirm('Force override resources...?')) {
                    $this->info('Halting scaffolding, try again with a another guard name...');
                }
                // Override resources
                $this->override = true;
            }
        }

        // Configurations
        $this->makeProgress("Registering configurations...");

        if ($this->exits && $this->override) {
            $this->makeProgress("Configurations registration skipped");
        } else {
            $this->registerConfigurations();
            $this->makeProgress('Configurations registered in ' . config_path('auth.php'));
        }

        // Models
        $this->makeProgress('Creating Model...');

        $model_path = $this->loadModel();

        $this->makeProgress("Model created at $model_path");

        // Factories
        $this->makeProgress("Creating Factory...");

        $factory_path = $this->loadFactory();

        $this->makeProgress("Factory created at $factory_path");

        // Notifications
        $this->makeProgress("Creating Notification...");

        $notification_path = $this->loadNotification();

        $this->makeProgress("Notification created at $notification_path");

        // Migrations
        $this->makeProgress('Creating Migrations...');

        if ($this->exits && $this->override) {
            $this->makeProgress("Migrations\' creation skipped");
        } else {
            $migrations_path = $this->loadMigrations();
            $this->makeProgress("Migrations created at $migrations_path");
        }

        // Controllers
        $this->makeProgress("Creating Controllers...");

        $controllers_path = $this->loadControllers();

        $this->makeProgress("Controllers created at $controllers_path");

        // Views
        $this->makeProgress("Creating Views...");

        $views_path = $this->loadViews();

        $this->makeProgress("Views created at $views_path");

        // Routes
        $this->makeProgress("Creating Routes...");

        $routes_path = $this->loadRoutes();

        $this->makeProgress("Routes created at $routes_path");

        // Routes Service Provider
        $this->makeProgress("Registering Routes Service Provider...");

        if ($this->exits && $this->override) {
            $this->makeProgress("Routes service provider registration skipped");
        } else {
            $routes_sp_path = $this->registerRoutes();
            $this->makeProgress("Routes registered in service provider: $routes_sp_path");
        }

        // Middleware
        $this->makeProgress("Creating Middleware...");

        $middleware_path = $this->loadMiddleware();

        $this->makeProgress("Middleware created at $middleware_path");

        // Route Middleware
        $this->makeProgress("Registering route middleware...");

        if ($this->exits && $this->override) {
            $this->makeProgress("Route middleware registration skipped");
        } else {
            $kernel_path = $this->registerRouteMiddleware();
            $this->makeProgress("Route middleware registered in $kernel_path");
        }

        $this->finishProgress('Installation complete.');
        // $progress->finish();

        // $this->info('Installation complete.');
    }
}
