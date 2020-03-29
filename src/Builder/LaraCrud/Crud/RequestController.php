<?php

namespace kallbuloso\Karl\Builder\LaraCrud\Crud;

use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;
use kallbuloso\Karl\Builder\LaraCrud\Contracts\Crud;
use kallbuloso\Karl\Builder\LaraCrud\Helpers\ClassInspector;
use kallbuloso\Karl\Builder\LaraCrud\Helpers\Helper;
use kallbuloso\Karl\Builder\LaraCrud\Helpers\TemplateManager;

class RequestController implements Crud
{
    use Helper;

    /**
     * @var
     */
    protected $table;

    /**
     * @var \Illuminate\Database\Eloquent\Model
     */
    protected $model;
    /**
     * @var string
     */
    protected $controllerNs = '';

    /**
     * @var string
     */
    protected $controllerName;

    /**
     * @var ClassInspector
     */
    protected $classInspector;

    /**
     * Request Class parent Namespace.
     *
     * @var string
     */
    protected $namespace;
    /**
     * Name of the folder where Request Classes will be saved.
     *
     * @var string
     */
    protected $folderName = '';

    /**
     * @var string
     */
    protected $template;

    /**
     * @var array
     */
    protected $methods = ['index', 'show', 'create', 'store', 'update', 'destroy'];

    /**
     * @var bool
     */
    private $policy;

    /**
     * RequestControllerCrud constructor.
     *
     * @param \Illuminate\Database\Eloquent\Model $model
     * @param string                              $controller
     * @param bool                                $api
     * @param string                              $name
     *
     * @throws \Exception
     */
    public function __construct(\Illuminate\Database\Eloquent\Model $model, $controller = '', $api = false, $name = '')
    {
        $moduleEnabled = config('karl.laracrud.modules.enabled') == true ?? false;
        $modulePath = config('karl.laracrud.modules.rootPath').'\\'.config('karl.laracrud.modules.vendorPath');

        $this->model = $model;
        $policies = Gate::policies();
        $this->policy = $policies[get_class($this->model)] ?? false;

        $controllerNs = !empty($api) ? config('karl.laracrud.controller.apiNamespace', 'App\Http\Controllers\Api') : config('karl.laracrud.controller.namespace', 'App\Http\Controllers');

        $this->controllerNs = $moduleEnabled
                ? $modulePath . '\\' . $controllerNs
                : $this->getFullNS($controllerNs);

        // $this->controllerNs = $cNs;
        $this->table = $model->getTable();
        $this->folderName = !empty($name) ? $name : $this->table;
        $this->template = !empty($api) ? 'api' : 'web';

        if (!empty($controller)) {
            if (!class_exists($controller)) {
                $this->controllerName = $this->controllerNs . '\\' . $controller;
            }

            if (!class_exists($this->controllerName)) {
                throw new \Exception('Controller ' . $this->controllerName . ' does not exists');
            }

            $this->classInspector = new ClassInspector($this->controllerName);
            $requestNs = !empty($api) ? config('karl.laracrud.request.apiNamespace') : config('karl.laracrud.request.namespace');

            $nSc = $moduleEnabled
                    ? $modulePath . '\\' . $requestNs
                    : $this->getFullNS(trim($requestNs, '/'));

            $this->namespace = $nSc . '\\' . ucfirst(Str::camel($this->folderName));
            $this->modelName = $this->getModelName($this->table);
        }
    }

    /**
     * Process template and return complete code.
     *
     * @param string $authorization
     *
     * @return mixed
     */
    public function template($authorization = 'true')
    {
        $tempMan = new TemplateManager('request/' . $this->template . '/template.txt', [
            'namespace' => $this->namespace,
            'requestClassName' => $this->modelName,
            'authorization' => $authorization,
            'rules' => implode("\n", []),
        ]);

        return $tempMan->get();
    }

    /**
     * Get code and save to disk.
     *
     * @return mixed
     *
     * @throws \Exception
     */
    public function save()
    {
        $this->checkPath('');
        $publicMethods = $this->classInspector->publicMethods;

        if (!empty($publicMethods)) {
            foreach ($publicMethods as $method) {
                $folderPath = base_path($this->toPath($this->namespace));
                $this->modelName = $this->getModelName($method);
                $filePath = $folderPath . '/' . $this->modelName . '.php';

                if (file_exists($filePath)) {
                    continue;
                }
                $isApi = 'api' == $this->template ? true : false;
                if (in_array($method, ['create', 'store'])) {
                    $requestStore = new Request($this->model, ucfirst(Str::camel($this->folderName)) . '/' . $this->modelName, $isApi);
                    $requestStore->setAuthorization($this->getAuthCode('create'));
                    $requestStore->save();
                } elseif (in_array($method, ['edit', 'update'])) {
                    $requestUpdate = new Request($this->model, ucfirst(Str::camel($this->folderName)) . '/' . $this->modelName, $isApi);
                    $requestUpdate->setAuthorization($this->getAuthCode('update'));
                    $requestUpdate->save();
                } else {
                    $auth = 'true';
                    if ('show' === $method) {
                        $auth = $this->getAuthCode('view');
                    } elseif ('destroy' === $method) {
                        $auth = $this->getAuthCode('delete');
                    } else {
                        $auth = $this->getAuthCode($method);
                    }
                    $model = new \SplFileObject($filePath, 'w+');
                    $model->fwrite($this->template($auth));
                }
            }
        }
    }

    private function getAuthCode($methodName)
    {
        $auth = 'true';
        if (class_exists($this->policy) && method_exists($this->policy, $methodName)) {
            if (in_array($methodName, ['index', 'create', 'store'])) {
                $code = '\\' . get_class($this->model) . '::class)';
            } else {
                $modelName = (new \ReflectionClass($this->model))->getShortName();
                $code = '$this->route(\'' . strtolower($modelName) . '\'))';
            }
            $auth = 'auth()->user()->can(\'' . $methodName . '\', ' . $code;
        }

        return $auth;
    }
}
