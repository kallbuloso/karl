<?php

namespace kallbuloso\Karl\Commands\LaraCrud;

use Illuminate\Console\Command;
use kallbuloso\Karl\Builder\LaraCrud\Crud\Request as RequestCrud;
use kallbuloso\Karl\Builder\LaraCrud\Crud\RequestController as RequestControllerCrud;
use kallbuloso\Karl\Builder\LaraCrud\Crud\RequestResource as RequestResourceCrud;
use kallbuloso\Karl\Builder\LaraCrud\Helpers\Helper;

class Request extends Command
{
    use Helper;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'laracrud:request
        {model : Eloquent Model name}
        {name? : Custom name of your Request. e.g. MyPostRequest }
        {--c|controller= : Create individual Request class for each public method of this controller.}
        {--r|resource= : Pass list of Resource method name e.g. --resource=index,show or pass --resource=all for a all of Resourceful method}
        {--api : whether its an API Request}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a request class based on Model';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        try {
            $modelName = $this->argument('model');
            $modelFullName = $this->modelFullName($modelName);
            $modelObj = new $modelFullName();
            $name = $this->argument('name');
            $controller = $this->option('controller');
            $resource = $this->option('resource');
            $api = $this->option('api');

            if (!empty($controller)) {
                $requestController = new RequestControllerCrud($modelObj, $controller, $api, $name);
                $requestController->save();
                $this->info('Request controller classes created successfully');
            } elseif (!empty($resource)) {
                $methods = 'all' === $resource ? false : explode(',', $resource);
                $requestResource = new RequestResourceCrud($modelObj, $methods, $api, $name);
                $requestResource->save();
                $this->info('Request resource classes created successfully');
            } else {
                $requestCrud = new RequestCrud($modelObj, $name, $api);
                $requestCrud->save();
                $this->info('Request class created successfully');
            }
        } catch (\Exception $ex) {
            $this->error($ex->getMessage() . ' on line ' . $ex->getLine() . ' in ' . $ex->getFile());
        }
    }

    private function modelFullName($model)
    {
        if (config('karl.laracrud.modules.enabled') == true) {
            $modelNamespace = config('karl.laracrud.modules.rootPath').'\\'.config('karl.laracrud.modules.vendorPath').'\\'.config('karl.laracrud.model.namespace');
        } else {
            $modelNamespace = $this->getFullNS(config('karl.laracrud.model.namespace', 'App'));
        }
        // $modelNamespace = $this->getFullNS(config('karl.laracrud.model.namespace', 'App'));
        if (!class_exists($model)) {
            return $modelNamespace . '\\' . $model;
        }

        return false;
    }
}
