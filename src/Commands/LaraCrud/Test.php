<?php

namespace kallbuloso\Karl\Commands\LaraCrud;

use Illuminate\Console\Command;
use kallbuloso\Karl\Builder\LaraCrud\Crud\Test as TestCrud;
use kallbuloso\Karl\Builder\LaraCrud\Helpers\Helper;

class Test extends Command
{
    use Helper;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'laracrud:test
        {controller : Controller Name}
        {--api : Whether its an API Controller Test}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create test based on Controller class';

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
            $controllers = [];
            $controller = $this->argument('controller');
            $api = $this->option('api');
            $namespace = $api == true ? config('karl.laracrud.controller.apiNamespace') : config('karl.laracrud.controller.namespace');
            $namespace = $this->getFullNS($namespace);

            if ('all' == $controller) {
                $path = $this->toPath($namespace);
                $dirIt = new \RecursiveDirectoryIterator(base_path($path));
                $rit = new \RecursiveIteratorIterator($dirIt);

                while ($rit->valid()) {
                    if (!$rit->isDot()) {
                        $controllers[] = rtrim($namespace, '\\') . '\\' . str_replace(
                            '.php',
                            '',
                            str_replace('/', '\\', $rit->getSubPathName())
                        );
                    }
                    $rit->next();
                }
                $testCrud = new TestCrud($controllers, $api);
            } else {
                $controller = str_replace('/', '\\', $controller);
                if (!stripos(rtrim($namespace, '\\') . '\\', $controller)) {
                    $controller = rtrim($namespace, '\\') . '\\' . $controller;
                }

                $testCrud = new TestCrud($controller, $api);
            }

            $testCrud->save();
            if (!empty($testCrud->errors)) {
                $this->error(implode(', ', $testCrud->errors));
            } else {
                $this->info('Test created successfully');
            }
        } catch (\Exception $ex) {
            $this->error($ex->getMessage() . ' on line ' . $ex->getLine() . ' in ' . $ex->getFile());
        }
    }
}
