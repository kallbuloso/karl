<?php

namespace kallbuloso\Karl\Builder\MultiAuth;

use Exception;
use RuntimeException;
use Illuminate\Container\Container;
use Illuminate\Support\Facades\Log;

trait MultiAuthTrait
{

    protected $stub_path = __DIR__ . '/stubs';

    protected $exits = false;

    protected $override = false;


    /**
     * Get project namespace
     * Default: App
     * @return string
     */
    protected function getNamespace()
    {
        $namespace = Container::getInstance()->getNamespace();
        return rtrim($namespace, '\\');
    }

    /**
     * Get project namespace Model
     * Default: App
     * @return string
     */
    protected function getModelNamespace()
    {
        $model_path = null;
        if (!config('karl.model_path') == null) {
            $model_path = Container::getInstance()->getNamespace() . config('karl.model_path');
        } else {
            $model_path = $this->getNamespace();
        }

        return $model_path;
    }

    /**
     * Parse guard name
     * Get the guard name in different cases
     * @param string $name
     * @return array
     */
    protected function parseName($name = null)
    {
        if (!$name)
            $name = $this->name;

        return $parsed = array(
            '{{namespace}}' => $this->getNamespace(),
            '{{modelNamespace}}' => $this->getModelNamespace(),
            '{{pluralCamel}}' => str_plural(camel_case($name)),
            '{{pluralSlug}}' => str_plural(str_slug($name)),
            '{{pluralSnake}}' => str_plural(snake_case($name)),
            '{{pluralClass}}' => str_plural(studly_case($name)),
            '{{singularCamel}}' => str_singular(camel_case($name)),
            '{{singularSlug}}' => str_singular(str_slug($name)),
            '{{singularSnake}}' => str_singular(snake_case($name)),
            '{{singularClass}}' => str_singular(studly_case($name)),
        );
    }

    /**
     * Register configurations
     * Add guard configurations to config/auth.php
     */
    protected function registerConfigurations()
    {
        try {

            $auth = file_get_contents(config_path('auth.php'));

            $data_map = $this->parseName();

            /********** Guards **********/

            $guards = file_get_contents($this->stub_path . '/config/guards.stub');

            // compile stub...
            $guards = strtr($guards, $data_map);

            $guards_bait = "'guards' => [";

            $auth = str_replace($guards_bait, $guards_bait . $guards, $auth);

            /********** Providers **********/

            $providers = file_get_contents($this->stub_path . '/config/providers.stub');

            // compile stub...
            $providers = strtr($providers, $data_map);

            $providers_bait = "'providers' => [";

            $auth = str_replace($providers_bait, $providers_bait . $providers, $auth);

            /********** Passwords **********/

            $passwords = file_get_contents($this->stub_path . '/config/passwords.stub');

            // compile stub...
            $passwords = strtr($passwords, $data_map);

            $passwords_bait = "'passwords' => [";

            $auth = str_replace($passwords_bait, $passwords_bait . $passwords, $auth);

            // Overwrite config file
            file_put_contents(config_path('auth.php'), $auth);

        } catch (Exception $ex) {
            throw new \RuntimeException($ex->getMessage());
        }
    }

    /**
     * Load model
     * @return string
     */
    protected function loadModel()
    {
        try {

            $stub = file_get_contents($this->stub_path . '/model.stub');

            $data_map = $this->parseName();

            $model = strtr($stub, $data_map);

            $modelPath = null;
            if (!config('karl.model_path') == null) {
                $modelPath = config('karl.model_path'). "\\";

                if (!$this->laravel['files']->isDirectory($directory = dirname($modelPath))) {
                    $this->laravel['files']->makeDirectory($directory, 0777, true);
                }

            }

            $model_path = app_path($modelPath . $data_map['{{singularClass}}'] . '.php');

            file_put_contents($model_path, $model);

            return $model_path;

        } catch (Exception $ex) {
            throw new \RuntimeException($ex->getMessage());
        }
    }

    /**
     * Load factory
     * @return string
     */
    protected function loadFactory()
    {
        try {

            $stub = file_get_contents($this->stub_path . '/factory.stub');

            $data_map = $this->parseName();

            $factory = strtr($stub, $data_map);

            $factory_path = database_path('factories/' . $data_map['{{singularClass}}'] . 'Factory.php');

            file_put_contents($factory_path, $factory);

            return $factory_path;

        } catch (Exception $ex) {
            throw new \RuntimeException($ex->getMessage());
        }
    }

    /**
     * Load notification
     * @return string
     */
    protected function loadNotification()
    {
        try {

            $data_map = $this->parseName();

            $notifications_path = app_path('Notifications\\' . $data_map['{{singularClass}}'] . '/Auth');

            $notifications = array(
                [
                    'stub' => $this->stub_path . '/Notifications/ResetPassword.stub',
                    'path' => $notifications_path . '/ResetPassword.php',
                ],
                [
                    'stub' => $this->stub_path . '/Notifications/VerifyEmail.stub',
                    'path' => $notifications_path . '/VerifyEmail.php',
                ],
            );

            foreach ($notifications as $notification) {
                $stub = file_get_contents($notification['stub']);
                $complied = strtr($stub, $data_map);

                $dir = dirname($notification['path']);
                if (!is_dir($dir)) {
                    mkdir($dir, 0755, true);
                }

                file_put_contents($notification['path'], $complied);
            }

            return $notifications_path;

        } catch (Exception $ex) {
            throw new \RuntimeException($ex->getMessage());
        }
    }

    /**
     * Load migrations
     * @return string
     */
    protected function loadMigrations()
    {
        try {

            $data_map = $this->parseName();

            $signature = date('Y_m_d_His');

            $migrations = array(
                [
                    'stub' => $this->stub_path . '/migrations/provider.stub',
                    'path' => database_path('migrations/' . $signature . '_create_' . $data_map['{{pluralSnake}}'] . '_table.php'),
                ],
                [
                    'stub' => $this->stub_path . '/migrations/password_resets.stub',
                    'path' => database_path('migrations/' . $signature . '_create_' . $data_map['{{singularSnake}}'] . '_password_resets_table.php'),
                ],
            );

            foreach ($migrations as $migration) {
                $stub = file_get_contents($migration['stub']);
                $complied = strtr($stub, $data_map);

                $dir = dirname($migration['path']);
                if (!is_dir($dir)) {
                    mkdir($dir, 0755, true);
                }

                file_put_contents($migration['path'], $complied);
            }

            return database_path('migrations');

        } catch (Exception $ex) {
            throw new \RuntimeException($ex->getMessage());
        }
    }

    /**
     * Load controllers
     * @return string
     */
    protected function loadControllers()
    {
        $data_map = $this->parseName();

        $guard = $data_map['{{singularClass}}'];

        $controllers_path = app_path('Http/Controllers/' . $guard);

        $controllers = array(
            [
                'stub' => $this->stub_path . '/Controllers/HomeController.stub',
                'path' => $controllers_path . '/HomeController.php',
            ],
            [
                'stub' => $this->stub_path . '/Controllers/Auth/ForgotPasswordController.stub',
                'path' => $controllers_path . '/Auth/ForgotPasswordController.php',
            ],
            [
                'stub' => $this->stub_path . '/Controllers/Auth/LoginController.stub',
                'path' => $controllers_path . '/Auth/LoginController.php',
            ],
            [
                'stub' => $this->stub_path . '/Controllers/Auth/RegisterController.stub',
                'path' => $controllers_path . '/Auth/RegisterController.php',
            ],
            [
                'stub' => $this->stub_path . '/Controllers/Auth/ResetPasswordController.stub',
                'path' => $controllers_path . '/Auth/ResetPasswordController.php',
            ],
            [
                'stub' => $this->stub_path . '/Controllers/Auth/VerificationController.stub',
                'path' => $controllers_path . '/Auth/VerificationController.php',
            ],
        );

        foreach ($controllers as $controller) {
            $stub = file_get_contents($controller['stub']);
            $complied = strtr($stub, $data_map);

            $dir = dirname($controller['path']);
            if (!is_dir($dir)) {
                mkdir($dir, 0755, true);
            }

            file_put_contents($controller['path'], $complied);
        }

        return $controllers_path;
    }

    /**
     * Load views
     * @return string
     */
    protected function loadViews()
    {
        $data_map = $this->parseName();

        $guard = $data_map['{{singularSlug}}'];

        $views_path = resource_path('views/' . $guard);

        $views = array(
            [
                'stub' => $this->stub_path . '/views/home.blade.stub',
                'path' => $views_path . '/home.blade.php',
            ],
            [
                'stub' => $this->stub_path . '/views/layouts/app.blade.stub',
                'path' => $views_path . '/layouts/app.blade.php',
            ],
            [
                'stub' => $this->stub_path . '/views/auth/login.blade.stub',
                'path' => $views_path . '/auth/login.blade.php',
            ],
            [
                'stub' => $this->stub_path . '/views/auth/register.blade.stub',
                'path' => $views_path . '/auth/register.blade.php',
            ],
            [
                'stub' => $this->stub_path . '/views/auth/verify.blade.stub',
                'path' => $views_path . '/auth/verify.blade.php',
            ],
            [
                'stub' => $this->stub_path . '/views/auth/passwords/email.blade.stub',
                'path' => $views_path . '/auth/passwords/email.blade.php',
            ],
            [
                'stub' => $this->stub_path . '/views/auth/passwords/reset.blade.stub',
                'path' => $views_path . '/auth/passwords/reset.blade.php',
            ],
        );

        foreach ($views as $view) {
            $stub = file_get_contents($view['stub']);
            $complied = strtr($stub, $data_map);

            $dir = dirname($view['path']);
            if (!is_dir($dir)) {
                mkdir($dir, 0755, true);
            }

            file_put_contents($view['path'], $complied);
        }

        return $views_path;
    }

    /**
     * Load routes
     * @return string
     */
    protected function loadRoutes()
    {
        $data_map = $this->parseName();

        $guard = $data_map['{{singularSlug}}'];

        $routes_path = base_path('routes/' . $guard . '.php');

        $routes = array(
            'stub' => $this->stub_path . '/routes/routes.stub',
            'path' => $routes_path,
        );

        $stub = file_get_contents($routes['stub']);
        $complied = strtr($stub, $data_map);

        file_put_contents($routes['path'], $complied);

        return $routes_path;
    }

    /**
     * Register routes
     * @return string
     */
    protected function registerRoutes()
    {
        try {

            $provider_path = app_path('Providers/RouteServiceProvider.php');

            $provider = file_get_contents($provider_path);

            $data_map = $this->parseName();

            /********** Function **********/

            $stub = $this->stub_path . '/routes/map.stub';

            $map = file_get_contents($stub);

            $map = strtr($map, $data_map);

            $map_bait = "    /**\n" . '     * Define the "web" routes for the application.';

            $provider = str_replace($map_bait, $map . $map_bait, $provider);

            /********** Function Call **********/

            $map_call = file_get_contents($this->stub_path . '/routes/map_call.stub');

            $map_call = strtr($map_call, $data_map);

            $map_call_bait = '$this->mapWebRoutes();';

            $provider = str_replace($map_call_bait, $map_call_bait . $map_call, $provider);

            // Overwrite config file
            file_put_contents($provider_path, $provider);

            return $provider_path;

        } catch (Exception $ex) {
            throw new \RuntimeException($ex->getMessage());
        }
    }

    /**
     * Load middleware
     * @return string
     */
    protected function loadMiddleware()
    {
        try {

            $data_map = $this->parseName();

            $middleware_path = app_path('Http/Middleware');

            $middlewares = array(
                [
                    'stub' => $this->stub_path . '/Middleware/RedirectIfAuthenticated.stub',
                    'path' => $middleware_path . '/RedirectIf' . $data_map['{{singularClass}}'] . '.php',
                ],
                [
                    'stub' => $this->stub_path . '/Middleware/RedirectIfNotAuthenticated.stub',
                    'path' => $middleware_path . '/RedirectIfNot' . $data_map['{{singularClass}}'] . '.php',
                ],
                [
                    'stub' => $this->stub_path . '/Middleware/EnsureEmailIsVerified.stub',
                    'path' => $middleware_path . '/Ensure' . $data_map['{{singularClass}}'] . 'EmailIsVerified.php',
                ],
            );

            foreach ($middlewares as $middleware) {
                $stub = file_get_contents($middleware['stub']);
                file_put_contents($middleware['path'], strtr($stub, $data_map));
            }

            return $middleware_path;

        } catch (Exception $ex) {
            throw new \RuntimeException($ex->getMessage());
        }
    }

    /**
     * Register middleware
     * @return string
     */
    protected function registerRouteMiddleware()
    {
        try {

            $data_map = $this->parseName();

            $kernel_path = app_path('Http/Kernel.php');

            $kernel = file_get_contents($kernel_path);

            /********** Route Middleware **********/

            $route_mw = file_get_contents($this->stub_path . '/Middleware/Kernel.stub');

            $route_mw = strtr($route_mw, $data_map);

            $route_mw_bait = 'protected $routeMiddleware = [';

            $kernel = str_replace($route_mw_bait, $route_mw_bait . $route_mw, $kernel);

            // Overwrite config file
            file_put_contents($kernel_path, $kernel);

            return $kernel_path;

        } catch (Exception $ex) {
            throw new \RuntimeException($ex->getMessage());
        }
    }
}
