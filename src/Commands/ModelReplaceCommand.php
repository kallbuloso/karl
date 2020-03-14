<?php

namespace kallbuloso\Karl\Commands;

use Illuminate\Console\Command;
use kallbuloso\Karl\Helpers\Helpers;
use kallbuloso\Karl\Helpers\Progressbar;
use kallbuloso\Karl\Builder\ModelReplace\ModelReplaceTrait;

class ModelReplaceCommand extends Command
{

    use ModelReplaceTrait, Progressbar, Helpers;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'karl:model-replace {model=Models}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Replace default Models from path Model or other of your choice';

    /**
     * Execute the console command.
     *
     * @return void
     *
     * @throws \InvalidArgumentException
     */
    public function handle()
    {
        $modelName = $this->getArgName();

        $this->startProgressBar(9, "Creating path \"$modelName\" end replacing model User.php");

        $this->makeProgress("Replacing User namespace");
        $this->exportAuthToPath($modelName);

        $this->makeProgress("Replacing files of User");
        $this->replace(app_path($modelName.'\\User.php'), "App", 'App\\'. $modelName);

            $paths = [
                    'Auth/Register' => app_path('Http\\Controllers\\Auth\\RegisterController.php'),
                    'Auth/Config' => base_path('config\\auth.php'),
                    'Auth/Services' => base_path('config\\services.php'),
                    'Auth/UserFactory' => base_path('database\\factories\\UserFactory.php'),
                ];
        foreach ($paths as $key => $path)
        {
            $this->makeProgress("Replacing file of $key");
            $this->replace($path,'App\\User', 'App\\'.$modelName.'\\User');
        }

        $this->makeProgress("Creating ModelCommand");
        $this->setModelCommand('Console\\Commands\\ModelMakeCommand.php');

        $this->makeProgress("Replacing command in Kernel");
        $this->replace(
            app_path('Console\\kernel.php'),
            'protected $commands = [',
            "protected \$commands = [ \n\t\t Commands\ModelMakeCommand::class"
        );

        // Finished creating the package, end of the progress bar
        $this->finishProgress('Model Path created successfully!');
        $this->dumpAutoloads();
    }

    /**
     * Get Argument Name
     *
     * @return void
     */
    protected function getArgName()
    {
        if (static::hasMacro($this->argument('model'))) {
            return call_user_func(static::$macros[$this->argument('model')], $this);
        }
        return $this->argument('model');
    }
}
