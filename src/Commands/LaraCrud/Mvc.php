<?php

namespace kallbuloso\Karl\Commands\LaraCrud;

use Illuminate\Console\Command;
use kallbuloso\Karl\Builder\LaraCrud\View\Edit;
use kallbuloso\Karl\Builder\LaraCrud\View\Show;
use kallbuloso\Karl\Builder\LaraCrud\Crud\Model;
use kallbuloso\Karl\Builder\LaraCrud\View\Index;
use kallbuloso\Karl\Builder\LaraCrud\View\Create;
use kallbuloso\Karl\Builder\LaraCrud\Crud\Request;
use kallbuloso\Karl\Builder\LaraCrud\Crud\RouteCrud;
use kallbuloso\Karl\Builder\LaraCrud\Crud\Controller;
use kallbuloso\Karl\Builder\LaraCrud\Crud\RequestResource;
use kallbuloso\Karl\Builder\LaraCrud\Helpers\Helper;

class Mvc extends Command
{
    use Helper;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'laracrud:mvc {table : MySQl Table name} {--api : Whether its an API MVC}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create Request, Model, Controller, View based on table';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        try {
            $table = $this->argument('table');
            $api = $this->option('api');
            Request::checkMissingTable($table);

            try {
                $modelCrud = new Model($table);
                $modelCrud->save();
                $modelNs = $modelCrud->getFullModelName();
                $model = new $modelNs();
                $this->info('Model class created successfully');
            } catch (\Exception $e) {
                $this->error($e->getMessage());
                exit();
            }

            try {
                $requestCrud = new RequestResource($model, false, $api);
                $requestCrud->save();
                $this->info('Request classes created successfully');
            } catch (\Exception $e) {
                $this->error($e->getMessage());
            }

            try {
                $controllerCrud = new Controller($modelNs, false, false, $api);
                $controllerCrud->save();
                $this->info('Controller class created successfully');
            } catch (\Exception $e) {
                $this->error($e->getMessage());
            }

            try {
                $controllers = [];
                $controller = $modelCrud->modelName().'Controller';

                $namespace = $api == true ? config('karl.laracrud.controller.apiNamespace') : config('karl.laracrud.controller.namespace');

                if (config('karl.laracrud.modules.enabled') == true) {
                    $namespace = config('karl.laracrud.modules.rootPath').'\\'.config('karl.laracrud.modules.vendorPath').'\\'.$namespace;
                } else {
                    $namespace = $this->getFullNS($namespace);
                }

                if ($controller == 'all') {
                    $path = $this->toPath($namespace);
                    $dirIt = new \RecursiveDirectoryIterator(base_path($path));
                    $rit = new \RecursiveIteratorIterator($dirIt);
                    while ($rit->valid()) {
                        if (!$rit->isDot()) {
                            $controllers[] = rtrim($namespace, '\\') . '\\' . str_replace('', str_replace('/', '\\', $rit->getSubPathName()));
                        }
                        $rit->next();
                    }
                    $routeCrud = new RouteCrud($controllers, $api);
                } else {
                    $controller = str_replace('/', '\\', $controller);
                    if (!stripos(rtrim($namespace, '\\') . '\\', $controller)) {
                        $controller = rtrim($namespace, '\\') . '\\' . $controller;
                    }

                    $routeCrud = new RouteCrud($controller, $api);
                }
                $routeCrud->save();
                $this->info('Routes created successfully');
            } catch (\Exception $e) {
                $this->error($e->getMessage());
            }

            if ($api) {
                $this->info('API resources created successfully');

                return;
            }

            $this->warn('Creating views files');
            try {
                $indexPage = new Index($model);
                $indexPage->save();
                $this->info('Index page created successfully');

                $showPage = new Show($model);
                $showPage->save();
                $this->info('Show page created successfully');

                $createPage = new Create($model);
                $createPage->save();
                $this->info('Create page created successfully');

                $edit = new Edit($model);
                $edit->save();
                $this->info('Edit page created successfully');
            } catch (\Exception $e) {
                $this->error($e->getMessage());
            }
        } catch (\Exception $ex) {
            $this->error($ex->getMessage() . ' on line ' . $ex->getLine() . ' in ' . $ex->getFile());
        }
    }
}
