<?php

namespace kallbuloso\Karl\Commands\LaraCrud;

use Illuminate\Console\Command;
use kallbuloso\Karl\Builder\LaraCrud\Crud\Controller as ControllerCrud;
use kallbuloso\Karl\Builder\LaraCrud\Crud\Policy;
use kallbuloso\Karl\Builder\LaraCrud\Crud\RequestResource as RequestResourceCrud;
use kallbuloso\Karl\Builder\LaraCrud\Helpers\Helper;

class Controller extends Command
{
    use Helper;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'laracrud:controller
      {model : Name of the Eloquent Model.}
      {name? : Custom Controller Name}
      {--o|only= : If you want to create partial resourceful controller. e.g. --only=index,show}
      {--api : whether its an API controller or now}
      {--p|parent= : Generate a nested resource controller class. Give the Parent Eloquent Model name. e.g --parent=Post}
      {--w|with= : Create Custom Request Classes or Policy along with Newly created Controller. e.g --with=request,policy }';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a Controller based on Model';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        try {
            $model = $this->argument('model');
            $name = $this->argument('name');
            $only = $this->option('only');
            $api = $this->option('api');
            $with = $this->option('with');
            $withArr = !empty($with) ? explode(',', $with) : [];
            $onlyArr = !empty($only) ? explode(',', $only) : '';
            $parent = $this->option('parent');

            if (in_array('request', $withArr)) {
                $modelFullName = $this->modelFullName($model);
                if (class_exists($modelFullName)) {
                    $modelObj = new $modelFullName();
                    $requestResource = new RequestResourceCrud($modelObj->getTable(), false, $api);
                    $requestResource->setModel($modelFullName);
                    $requestResource->save();
                    $this->info('Request controller classes created successfully');
                }
            }

            $controllerCrud = new ControllerCrud($model, $name, $onlyArr, $api, $parent);
            $controllerCrud->save();
            $this->info('Controller class successfully created');

            if (in_array('policy', $withArr)) {
                $policyCrud = new Policy($model, $controllerCrud->getFullName());
                $policyCrud->save();
                $this->info('Policy class created successfully');
            }
        } catch (\Exception $ex) {
            $this->error($ex->getMessage() . ' on line ' . $ex->getLine() . ' in ' . $ex->getFile());
        }
    }

    private function modelFullName($model)
    {
        $modelNamespace = $this->getFullNS(config('karl.laracrud.model.namespace', 'App'));
        if (!class_exists($model)) {
            return $modelNamespace . '\\' . $model;
        }

        return false;
    }
}
