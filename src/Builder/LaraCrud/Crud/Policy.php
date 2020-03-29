<?php

namespace kallbuloso\Karl\Builder\LaraCrud\Crud;

use kallbuloso\Karl\Builder\LaraCrud\Contracts\Crud;
use kallbuloso\Karl\Builder\LaraCrud\Helpers\ClassInspector;
use kallbuloso\Karl\Builder\LaraCrud\Helpers\Helper;
use kallbuloso\Karl\Builder\LaraCrud\Helpers\TemplateManager;

class Policy implements Crud
{
    use Helper;
    /**
     * Controller Name prefix.
     * If Model Name is User and no controller name is supplier then it will be User and then Controller will be appended.
     * Its name will be UserController.
     *
     * @var string
     */
    protected $controllerName;

    /**
     * Model Name.
     *
     * @var string
     */
    protected $modelName;

    /**
     * Default Model Namespace. So if not namespace is specified on
     *  Model then this namespace will be added and check if model exists.
     *
     * @var type
     */
    protected $modelNameSpace = 'App';

    /**
     * @var string
     */
    protected $name = '';

    /**
     * @var
     */
    public $namespace;

    /**
     * Namespace version of subpath.
     *
     * @var type
     */
    protected $subNameSpace = '';

    /**
     * @var array
     */
    protected $only = ['index', 'show', 'create', 'update', 'destroy'];

    /**
     * Model Name without Namespace.
     *
     * @var type
     */
    protected $shortModelName;
    /**
     * @var string
     */
    protected $modelFullClass;

    /**
     * Policy constructor.
     *
     * @param $model
     * @param string $controller
     * @param string $name
     * @param string $only
     *
     * @throws \Exception
     */
    public function __construct($model, $controller = '', $name = '', $only = '')
    {
        $modelNamespace = $this->getFullNS(config('karl.laracrud.model.namespace', 'App'));
        // $modelNamespace = $this->getFullNS(null);
        $this->shortModelName = $model;
        $this->checkController($controller);

        if (!empty($only) && is_array($only)) {
            $this->only = $only;
        }

        if (0 !== substr_compare($modelNamespace, '\\', 0, 1)) {
            $modelNamespace = '\\' . $modelNamespace;
        }
        $this->modelNameSpace = $modelNamespace;

        $this->modelFullClass = $this->modelName = class_exists($model) ? $model : $this->modelNameSpace . '\\' . $model;
        $rNs = config('karl.laracrud.rootNamespace');
        $this->modelFullClass = str_replace('\\' . $rNs, $rNs, $this->modelFullClass);

        $this->checkName($name);
        $this->namespace = $this->getFullNS(trim(config('karl.laracrud.policy.namespace'), ' / ')) . $this->subNameSpace;
    }

    /**
     * Process template and return complete code.
     *
     * @return mixed
     */
    public function template()
    {
        $methodsTemp = [];
        $tempCheck = new TemplateManager('policy/template.txt');
        foreach ($this->only as $method) {
            $fileName = $tempCheck->getFullPath("policy/$method.txt") ? "policy/$method.txt" : 'policy/default.txt';
            $methodsTemp[] = (new TemplateManager($fileName, [
                'method' => $method,
                'modelClass' => $this->shortModelName,
                'modelFullClass' => $this->modelFullClass,
                'modelClassVar' => lcfirst($this->shortModelName),
            ]))->get();
        }
        $userClass = !empty(config('auth.providers.users.model')) ? config('auth.providers.users.model') : config('karl.laracrud.model.namespace') . '\\User';

        return (new TemplateManager('policy/template.txt', [
            'namespace' => $this->namespace,
            'className' => $this->getClassName(),
            'modelFullClass' => $this->modelFullClass,
            'userClass' => $userClass,
            'methods' => implode("\n", $methodsTemp),
        ]))->get();
    }

    /**
     * Get code and save to disk.
     *
     * @return mixed
     */
    public function save()
    {
        $this->modelName = $this->getClassName();
        $filePath = $this->checkPath();
        $file = new \SplFileObject($filePath, 'w+');
        $file->fwrite($this->template());
        $file->fflush();
    }

    /**
     * @param $name
     */
    private function checkName($name)
    {
        if (!empty($name)) {
            if (false !== strpos($name, ' / ')) {
                $narr = explode(' / ', $name);
                $this->name = array_pop($narr);

                foreach ($narr as $p) {
                    $this->subNameSpace .= '\\' . $p;
                }
            } else {
                $this->name = $name;
            }
        }
    }

    /**
     * @param $controller
     *
     * @throws \Exception
     */
    private function checkController($controller)
    {
        if (!empty($controller)) {
            $this->controllerName = class_exists($controller) ? $controller : $this->getFullNS(config('karl.laracrud.controller.namespace') . '\\' . $controller);
            if (!class_exists($this->controllerName)) {
                throw new \Exception($controller . ' does not exists');
            }
            $classInspector = new ClassInspector($this->controllerName);
            $this->only = $classInspector->publicMethods;
        }
    }

    /**
     * @return type|string
     */
    private function getClassName()
    {
        return !empty($this->name) ? $this->name : $this->shortModelName . config('karl.laracrud.policy.classSuffix');
    }
}
