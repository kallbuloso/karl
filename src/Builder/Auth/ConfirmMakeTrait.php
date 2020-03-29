<?php

namespace kallbuloso\Karl\Builder\Auth;

use Illuminate\Container\Container;
use kallbuloso\Karl\Helpers\Helpers;

trait ConfirmMakeTrait
{
    use Helpers;

    /**
     * The app that need to be exported.
     *
     * @var array
     */
    protected $apps = [
        'app\Http\Controllers\Auth\LoginController.stub'            =>  'Http\Controllers\Auth\LoginController.php',
        'app\Http\Controllers\Auth\RegisterController.stub'         =>  'Http\Controllers\Auth\RegisterController.php',
        'app\Http\Controllers\Auth\ChangePasswordController.stub'   =>  'Http\Controllers\Auth\ChangePasswordController.php',
    ];

    /**
     * The mail that need to be exported.
     *
     * @var array
     */
    protected $mails = [
        'app\Mail\VerifyMail.stub'    =>  'Mail\VerifyMail.php',
    ];

    /**
     * The models that need to be exported.
     *
     * @var array
     */
    protected $models = [
        'app\models\User.stub'          =>  'User.php',
        'app\models\VerifyUser.stub'    =>  'VerifyUser.php',
    ];

    /**
     * The migrations that need to be exported.
     *
     * @var array
     */
    protected $migrations = [
        'database\migrations\2014_10_12_000000_create_users_table.stub'           =>  'database\migrations\2014_10_12_000000_create_users_table.php',
        'database\migrations\2020_10_12_000000_create_verify_users_table.stub'    =>  'database\migrations\2020_10_12_000000_create_verify_users_table.php',
        'database\factories\UserFactory.stub'       =>  'database\factories\UserFactory.php',
        'database\seeds\DatabaseSeeder.stub'        =>  'database\seeds\DatabaseSeeder.php',
        'database\seeds\UsersTableSeeder.stub'      =>  'database\seeds\UsersTableSeeder.php',
    ];

    /**
     * The views that need to be exported.
     *
     * @var array
     */
    protected $views = [
        'resources\views\emails\verifyUser.stub'    => 'emails\verifyUser.blade.php',
        'resources\views\auth\changepassword.stub'  => 'auth\changepassword.blade.php',
    ];

    /**
     * The routes that need to be exported.
     *
     * @var array
     */
    protected $routes = [
        'routes\route.stub' => 'routes\web.php',
    ];

    /**
     * Create the directories for the files.
     *
     * @return void
     */
    protected function createDirectories()
    {
        if (! is_dir($directory = $this->getAppNamespace().'Mail')) {
            mkdir($directory, 0755, true);
        }

        if (! is_dir($directory = $this->getViewPath('emails'))) {
            mkdir($directory, 0755, true);
        }

        if (! is_dir($directory = $this->getViewPath('auth'))) {
            mkdir($directory, 0755, true);
        }
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
            '{{namespace}}'=> $this->getAppNamespace(),
            '{{layoutsExtends}}' => config('karl.auth.layouts_extends') ?? 'layouts.app',
            '{{modelNamespace}}' => $this->getModelNamespace(),
            '{{redirectAfterConfirm}}' => config('karl.auth.confirm.redirect_after_confirm') ?? '/',
        );
    }

    /**
     * Export the authentication views.
     *
     * @return void
     */
    protected function exportMail()
    {
        foreach ($this->mails as $key => $value) {
            if (file_exists($mail = $this->getAppNamespace().$value) && ! $this->option('force')) {
                if (! $this->confirm("The [{$value}] mail already exists. Do you want to replace it?")) {
                    continue;
                }
            }

            $stubMail = $this->compileStub($key);

            $this->files->put($mail, $stubMail);
        }

        foreach ($this->views as $key => $value) {
            if (file_exists($view = $this->getViewPath($value)) && ! $this->option('force')) {
                if (! $this->confirm("The [{$value}] view already exists. Do you want to replace it?")) {
                    continue;
                }
            }

            $stubView = $this->compileStub($key);

            $this->files->put($view, $stubView);
        }
    }

    /**
     * Export the authentication views.
     *
     * @return void
     */
    protected function exportApps()
    {
        foreach ($this->apps as $key => $value) {

            $stubApp = $this->compileStub($key);

            if (file_exists($app = $this->getAppNamespace().$value) || $this->option('force')) {
                $this->files->put($app, $stubApp);
            }
        }
    }

    /**
     * Export the authentication views.
     *
     * @return void
     */
    protected function exportModels()
    {
        foreach ($this->models as $key => $value) {

            $stubModel = $this->compileStub($key);

            if (file_exists($model = $this->getModelNamespace().'\\'.$value)) {
                $this->option('force');
            }
            $this->files->put($model, $stubModel);
        }
    }


    /**
     * Export the authentication views.
     *
     * @return void
     */
    protected function exportMigrations()
    {
        foreach ($this->migrations as $key => $value) {

            $stubMigration = $this->compileStub($key);

            if (file_exists($migration = base_path($value))) {
                $this->option('force');
            }
            $this->files->put($migration, $stubMigration);
        }
    }

    /**
     * Export the Controller.
     *
     * @return void
     */
    protected function exportRoute()
    {
        foreach ($this->routes as $key => $value) {

            $stubRoute = $this->compileStub($key);
            // $stubContent = file_get_contents($stubRoute);

            // dd($stubRoute);

            $routePath = base_path($value);

            $routeGetContent = file_get_contents($routePath);

            $routeSetContent = str_replace($stubRoute, "",$routeGetContent);

            // $routeContent = rtrim($routeContent, "\n$stubRoute");

            $routeSetContent .= $stubRoute;

            // dd($routeSetContent);

            if (file_exists($routePath) || $this->option('force')) {
                // $this->files->append($routePath, $routeContent);
                $this->replaceIn($routePath, $routeGetContent, $routeSetContent);
                return;
            } else{
                $this->files->append($routePath, $stubRoute);
            }
        }
    }

    /**
     * Compiles the Stub.
     *
     * @return string
     */
    protected function compileStub($stub)
    {
        $data_map = $this->parseName();

        $guards = file_get_contents(__DIR__.'/stubConfirm/'.$stub);

        return strtr($guards, $data_map);
    }

    /**
     * Get full view path relative to the app's configured view path.
     *
     * @param  string  $path
     * @return string
     */
    protected function getViewPath($path)
    {
        return implode(DIRECTORY_SEPARATOR, [
            config('view.paths')[0] ?? resource_path('views'), $path,
        ]);
    }

    /**
     * Get project namespace
     * Default: App
     * @return string
     */
    protected function getAppNamespace()
    {
        $namespace = Container::getInstance()->getNamespace();
        // return rtrim($namespace, '\\');
        return $namespace;
    }

    /**
     * Get project namespace
     * Default: App
     * @return string
     */
    protected function getModelNamespace()
    {
        if (config('karl.model_path')) {
            return $this->getAppNamespace() . config('karl.model_path');
        } else {
            return $this->getAppNamespace();
        }
    }
}
