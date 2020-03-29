<?php

namespace kallbuloso\Karl\Builder\LaraCrud\Crud;

use kallbuloso\Karl\Builder\LaraCrud\DbReader\Table;
use Illuminate\Database\Eloquent\Model;
use kallbuloso\Karl\Builder\LaraCrud\Contracts\Crud;
use kallbuloso\Karl\Builder\LaraCrud\Helpers\FakerColumn;
use kallbuloso\Karl\Builder\LaraCrud\Helpers\Helper;
use kallbuloso\Karl\Builder\LaraCrud\Helpers\TemplateManager;

class ModelFactory implements Crud
{
    use Helper;

    /**
     * @var Model
     */
    protected $model;

    /**
     * @var Table
     */
    protected $table;

    /**
     * @var string
     */
    protected $name;

    /**
     * ModelFactory constructor.
     *
     * @param Model  $model
     * @param string $name
     *
     * @throws \Exception
     */
    public function __construct($model, $name = '')
    {
        $this->name = $name;
        if (config('karl.laracrud.modules.enabled') == true) {
            $modelNamespace = config('karl.laracrud.modules.rootPath').'\\'.config('karl.laracrud.modules.vendorPath').'\\'.config('karl.laracrud.model.namespace');
        } else {
            $modelNamespace = $this->getFullNS(config('karl.laracrud.model.namespace'));
        }
        // $modelNamespace = $this->getFullNS(config('karl.laracrud.model.namespace', 'App'));
        // dd($modelNamespace);

        if (!class_exists($model)) {
            $model = $modelNamespace . '\\' . $model;
        }
        if (!class_exists($model)) {
            throw new \Exception('Model ' . $model . ' is not exists');
        }
        $this->model = new $model();
        $this->table = new Table($this->model->getTable());
    }

    public function save()
    {
        $path = config('karl.laracrud.modules.enabled') == true
                ? base_path(config('karl.laracrud.modules.rootPath').'\\'.config('karl.laracrud.modules.vendorPath').'\\database\\factories')
                : base_path(config('karl.laracrud.factory.path'));
        $name = $this->getName();
        if (file_exists($path . '/' . $name)) {
            throw new \Exception($name . ' already exists');
        }
        $factory = new \SplFileObject($path . '/' . $name . '.php', 'w+');
        $factory->fwrite($this->template());
    }

    /**
     * @return mixed|string
     */
    public function template()
    {
        return (new TemplateManager('factory/template.txt', [
            'modelClass' => get_class($this->model),
            'columns' => $this->makeColumns(),
        ]))->get();
    }

    /**
     * @return string
     */
    protected function makeColumns()
    {
        $arr = '';
        $columns = $this->table->columnClasses();
        foreach ($columns as $column) {
            if ($column->isProtected()) {
                continue;
            }
            $fakerColumn = new FakerColumn($column);
            $default = $fakerColumn->default();
            $columnValue = !empty($default) ? $default . ',' : '\'\',';
            $arr .= "\t\t" . '"' . $column->name() . '" => ' . $columnValue . PHP_EOL;
        }

        return $arr;
    }

    /**
     * @return string
     *
     * @throws \ReflectionException
     */
    protected function getName()
    {
        $suffix = config('karl.laracrud.factory.suffix', 'Factory');
        $class = new \ReflectionClass($this->model);
        $shortModelName = $class->getShortName();

        return !empty($this->name) ? $this->name : $shortModelName . $suffix;
    }
}
