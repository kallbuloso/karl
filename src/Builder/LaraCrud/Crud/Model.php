<?php

namespace kallbuloso\Karl\Builder\LaraCrud\Crud;

use Illuminate\Support\Str;
use kallbuloso\Karl\Builder\LaraCrud\DbReader\Table;
use kallbuloso\Karl\Builder\LaraCrud\Builder\Model as ModelBuilder;
use kallbuloso\Karl\Builder\LaraCrud\Contracts\Crud;
use kallbuloso\Karl\Builder\LaraCrud\Helpers\ForeignKey;
use kallbuloso\Karl\Builder\LaraCrud\Helpers\Helper;
use kallbuloso\Karl\Builder\LaraCrud\Helpers\TemplateManager;

class Model implements Crud
{
    use Helper;
    /**
     * Model Namespace. If not specified then default namespace will be used.
     * @var string
     */
    protected $namespace;

    /**
     * Name of Model class
     * @var string
     */
    protected $modelName;

    /**
     * @var Table
     */
    protected $table;

    /**
     * @var ModelBuilder
     */
    protected $modelBuilder;

    /**
     * Model constructor.
     * @param $table
     * @param $name  user define model and namespace. E.g. Models/MyUser will be saved as App\Models\MyUser
     */
    public function __construct($table, $name = '')
    {
        $this->table = new Table($table);
        $this->modelBuilder = $this->makeModelBuilders();
        $this->namespace = $this->getFullNS(config('karl.laracrud.model.namespace'));
        $this->modelName = $this->getModelName($table);
        if (!empty($name)) {
            $this->parseName($name);
        }
    }

    /**
     * Done all processing work and make the final code that is ready to save as php file
     * @return string
     */
    public function template()
    {
        $relations = $this->relations();
        $data = [
            'namespace' => $this->namespace,
            'modelName' => $this->modelName,
            'propertyDefiner' => config('karl.laracrud.model.propertyDefiner') ? implode("\n * ", array_reverse($this->modelBuilder->propertyDefiners)) : '',
            'methodDefiner' => config('karl.laracrud.model.methodDefiner') ? implode("\n * ", array_reverse($this->modelBuilder->methodDefiners)) : '',
            'tableName' => $this->table->name(),
            'constants' => $this->constants(),
            'guarded' => config('karl.laracrud.model.guarded') ? $this->guarded() : '',
            'fillable' => config('karl.laracrud.model.fillable') ? $this->fillable() : '',
            'dateColumns' => $this->dates(),
            'casts' => config('karl.laracrud.model.casts') ? $this->casts() : '',
            'relationShips' => $relations,
            'mutators' => config('karl.laracrud.model.mutators') ? $this->mutators() : '',
            'accessors' => config('karl.laracrud.model.accessors') ? $this->accessors() : '',
            'scopes' => config('karl.laracrud.model.scopes') ? $this->scopes() : ''
        ];
        $tempMan = new TemplateManager('model/template.txt', $data);
        return $tempMan->get();
    }

    /**
     * Save code as php file
     * @return mixed
     * @throws \Exception
     */
    public function save()
    {

        $filePath = $this->checkPath();
        if (file_exists($filePath)) {
            throw new \Exception($this->namespace . '\\' . $this->modelName . ' already exists');
        }
        $model = new \SplFileObject($filePath, 'w+');
        $model->fwrite($this->template());

    }

    /**
     * Make constant code
     * @return mixed
     */
    protected function constants()
    {
        return implode("\n", $this->modelBuilder->constants);
    }

    /**
     * Generate guarded code
     * @return string
     */
    protected function guarded()
    {
        if (config('karl.laracrud.model.guarded')) {
            $tempMan = new TemplateManager('model/guarded.txt');
            return $tempMan->get();
        }
        return '';
    }

    /**
     * Make fillable property
     * @return string
     */
    protected function fillable()
    {
        if (!config('karl.laracrud.model.fillable')) {
            return '';
        }
        //'methodDefiner' => config('karl.laracrud.model.methodDefiner') ? implode("\n * ", array_reverse($this->modelBuilder->methodDefiners)) : '',
        $tempMan = new TemplateManager('model/fillable.txt', ['columns' => implode(",\n\t\t", array_reverse($this->modelBuilder->fillable))]);
        return $tempMan->get();
    }

    /**
     * Making of dates property
     * @return string
     */
    protected function dates()
    {
        $tempMan = new TemplateManager('model/dates.txt', ['columns' => implode(",\n", array_reverse($this->modelBuilder->dates))]);
        return $tempMan->get();
    }

    /**
     * Making of casts property
     * @return string
     */
    protected function casts()
    {
        $tempMan = new TemplateManager('model/casts.txt', ['columns' => implode(",\n", array_reverse($this->modelBuilder->casts))]);
        return $tempMan->get();
    }

    /**
     * Making relationship code
     * @return string
     */
    protected function relations()
    {
        $temp = '';
        $otherKeys = $this->table->references();
        //print_r($this->modelBuilder->relations);
        foreach ($this->modelBuilder->relations as $relation) {
            $param = ",'" . $relation['foreign_key'] . "'";
            $tempMan = new TemplateManager('model/relationship.txt', [
                'relationShip' => $relation['name'],
                'modelName' => $relation['model'],
                'methodName' => lcfirst($relation['methodName']),
                'returnType' => ucfirst($relation['name']),
                'params' => $param
            ]);
            $temp .= $tempMan->get() . PHP_EOL;
            array_unshift($this->modelBuilder->propertyDefiners, '@property ' . $relation['methodName'] . ' $' . lcfirst($relation['model']) . ' ' . $relation['name']);
        }
        foreach ($otherKeys as $column) {
            $fk = new ForeignKey($column);

            if ($fk->isPivot) {
                $param = ",'" . $fk->table() . "'";
                $tempMan = new TemplateManager('model/relationship.txt', [
                    'relationShip' => ForeignKey::RELATION_BELONGS_TO_MANY,
                    'modelName' => $fk->modelName(),
                    'methodName' => Str::plural(lcfirst($fk->modelName())),
                    'returnType' => ucfirst(ForeignKey::RELATION_BELONGS_TO_MANY),
                    'params' => $param
                ]);
                array_unshift($this->modelBuilder->propertyDefiners, '@property \Illuminate\Database\Eloquent\Collection' . ' $' . lcfirst($fk->modelName()) . ' ' . ForeignKey::RELATION_BELONGS_TO_MANY);
            } else {
                $param = ",'" . $fk->column() . "'";
                $tempMan = new TemplateManager('model/relationship.txt', [
                    'relationShip' => ForeignKey::RELATION_HAS_MANY,
                    'modelName' => $fk->modelName(),
                    'methodName' => Str::plural(lcfirst($fk->modelName())),
                    'returnType' => ucfirst(ForeignKey::RELATION_HAS_MANY),
                    'params' => $param
                ]);
                array_unshift($this->modelBuilder->propertyDefiners, '@property \Illuminate\Database\Eloquent\Collection' . ' $' . lcfirst($fk->modelName()) . ' ' . ForeignKey::RELATION_HAS_MANY);
            }
            $temp .= $tempMan->get();

        }
        return $temp;

    }

    /**
     * making of scopes methods code
     * @return string
     */
    protected function scopes()
    {
        $tempMan = new TemplateManager('model/search_scope.txt', ['whereClause' => implode("\n", $this->modelBuilder->makeSearch())]);
        $scopes = implode("\n", array_reverse($this->modelBuilder->scopes));
        return $scopes . PHP_EOL . $tempMan->get();
    }

    /**
     * Making of mutators code
     * @return string
     */
    protected function mutators()
    {
        return implode("\n", array_reverse($this->modelBuilder->mutators));
    }

    /**
     * Making of accessors method code
     * @return string
     */
    protected function accessors()
    {
        return implode("\n", array_reverse($this->modelBuilder->accessors));
    }

    /**
     *
     */
    public function makeModelBuilders()
    {
        $builder = null;
        $columns = $this->table->columnClasses();

        foreach ($columns as $column) {
            if (empty($builder)) {
                $builder = new ModelBuilder($column);
            } else {
                $newBuilder = new ModelBuilder($column);
                $newBuilder->merge($builder);
                $builder = $newBuilder;
            }
        }
        return $builder;
    }

    /**
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function modelName()
    {
        return $this->modelName;
    }

    /**
     * @return string
     */
    public function getFullModelName()
    {
        return $this->namespace . '\\' . $this->modelName;
    }


}
