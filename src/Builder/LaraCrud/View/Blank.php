<?php

namespace kallbuloso\Karl\Builder\LaraCrud\View;

use kallbuloso\Karl\Builder\LaraCrud\DbReader\Table;
use kallbuloso\Karl\Builder\LaraCrud\Helpers\TemplateManager;

class Blank extends Page
{
    protected $form;

    public function __construct(Table $table, $name = '')
    {
        $this->table = $table;
        $this->setFolderName();
        $this->name = !empty($name) ? $name : config('karl.laracrud.view.page.create.name');
        parent::__construct();
    }

    /**
     * @return string
     */
    public function template()
    {
        return (new TemplateManager("view/{$this->version}/pages/blank.html", [
            'layout' => config('karl.laracrud.view.layout'),
            'table' => $this->table->name(),
            'indexRoute' => $this->getRouteName('index', $this->table->name()),
        ]))->get();
    }
}
