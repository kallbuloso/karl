<?php

namespace kallbuloso\Karl\Commands\LaraCrud;

use Illuminate\Console\Command;
use kallbuloso\Karl\Builder\LaraCrud\Crud\Policy as CrudPolicy;

class Policy extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'laracrud:policy
        {model  : Eloquent model name}
        {--c|controller= : Create policy for all of the public method of this controller. e.g. --controller=PostController}
        {--name= : Custom Name of the Policy. e.g. --name=MyPostPolicy}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a Policy class based on Controller or Model';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        try {
            $model = $this->argument('model');
            $controller = str_replace('/', '\\', $this->option('controller'));
            $name = $this->option('name');

            $policyCrud = new CrudPolicy($model, $controller, $name);
            $policyCrud->save();
            $this->info('Policy class created successfully');
        } catch (\Exception $ex) {
            $this->error($ex->getMessage() . ' on line ' . $ex->getLine() . ' in ' . $ex->getFile());
        }
    }
}
