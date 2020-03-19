<?php

namespace kallbuloso\Karl\Builder\Auth;

use Illuminate\Container\Container;
use kallbuloso\Karl\Helpers\Helpers;

trait AuthMakeTrait
{
    use Helpers;

    /**
     * The views that need to be exported.
     *
     * @var array
     */
    protected $views = [
        'auth/login.stub' => 'auth/login.blade.php',
        'auth/register.stub' => 'auth/register.blade.php',
        'auth/verify.stub' => 'auth/verify.blade.php',
        'auth/passwords/email.stub' => 'auth/passwords/email.blade.php',
        'auth/passwords/reset.stub' => 'auth/passwords/reset.blade.php'
    ];

    /**
     * The layouts that need to be exported.
     *
     * @var array
     */
    protected $layouts = [
        'layouts/app.stub' => 'layouts/app.blade.php'
    ];

    /**
     * The home blade that need to be exported.
     *
     * @var array
     */
    protected $homes = [
        'home.stub' => 'home.blade.php',
    ];

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
            '{{name_page}}' => config('karl.auth.page_home.name') ?? 'home',
            '{{name_controller}}' => ucfirst(config('karl.auth.page_home.name')) ?? 'Home',
            '{{redirect_page}}' => config('karl.auth.redirect_page') ?? '/',
        );
    }

    /**
     * Create the directories for the files.
     *
     * @return void
     */
    protected function createDirectories()
    {
        if (config('karl.auth.layout_path') == true) {
            if (! is_dir($directory = $this->getViewPath('layouts'))) {
                mkdir($directory, 0755, true);
            }
        }

        if (! is_dir($directory = $this->getViewPath('auth/passwords'))) {
            mkdir($directory, 0755, true);
        }
    }

    /**
     * Export the authentication views.
     *
     * @return void
     */
    protected function exportViews()
    {
        foreach ($this->views as $key => $value) {
            if (file_exists($view = $this->getViewPath($value)) && ! $this->option('force')) {
                if (! $this->confirm("The [{$value}] view already exists. Do you want to replace it?")) {
                    continue;
                }
            }

            $stubView = $this->compileStub(__DIR__.'/stubs/views/'.$key);

            $this->files->put($view, $stubView);
        }
    }

    /**
     * Export the layout view.
     *
     * @return void
     */
    protected function exportLayout()
    {
        if (config('karl.auth.layout_path') == true) {
            foreach ($this->layouts as $key => $value) {
                if (file_exists($layout = $this->getViewPath($value)) && ! $this->option('force')) {
                    if (! $this->confirm("The [{$value}] layout already exists. Do you want to replace it?")) {
                        return;
                    }
                }
            }

            $stubView = $this->compileStub(__DIR__.'/stubs/views/'.$key);

            $this->files->put($layout, $stubView);
        }
    }

    /**
     * Export the home view.
     *
     * @return void
     */
    protected function exportHome()
    {
        if (config('karl.auth.page_home.make_page') == true) {
            foreach ($this->homes as $key => $value) {
                if (file_exists($home = $this->getViewPath($value)) && ! $this->option('force')) {
                    if (! $this->confirm("The [{$value}] home already exists. Do you want to replace it?")) {
                        return;
                    }
                }
            }

            $stubView = $this->compileStub(__DIR__.'/stubs/views/'.$key);

            $this->files->put($home, $stubView);
        }
    }

    /**
     * Export the Controller.
     *
     * @return void
     */
    protected function exportController()
    {
        if (config('karl.auth.page_home.make_page') == true) {
            $nameController = ucfirst(config('karl.auth.page_home.name'));
            file_put_contents(
                app_path('Http/Controllers/'.$nameController.'Controller.php'),
                $this->compileControllerStub()
            );
        }
    }

    /**
     * Alter Redirect if auth.
     *
     * @return void
     */
    protected function alterRedirectIfAuth()
    {
        $path = app_path("Http\\Middleware\\RedirectIfAuthenticated.php");

        $search = [
            file_get_contents($path)
        ];

        $replace = [
            $this->compileStub(__DIR__.'/stubs/middleware/RedirectIfAuthenticated.stub')
        ];

        $this->replaceIn($path, $search, $replace);
    }


    /**
     * Export the Controller.
     *
     * @return void
     */
    protected function exportRoute()
    {
        $route = $this->compileStub(__DIR__.'/stubs/routes.stub');

        $routePath = base_path('routes/web.php');

        if (file_exists($routePath) && $this->option('force')) {

            $this->replaceIn($routePath, $route, $route);
            return;
        }

        $this->files->append(base_path('routes/web.php'), $route);
    }

    /**
     * Compiles the Stub.
     *
     * @return string
     */
    protected function compileStub($stub)
    {
        $data_map = $this->parseName();

        $guards = file_get_contents($stub);

        return strtr($guards, $data_map);
    }

    /**
     * Compiles the HomeController stub.
     *
     * @return string
     */
    protected function compileControllerStub()
    {
        return $this->compileStub(__DIR__.'/stubs/controllers/HomeController.stub');
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
}
