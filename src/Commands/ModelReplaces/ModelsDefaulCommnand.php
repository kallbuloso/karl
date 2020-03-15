<?php

namespace kallbuloso\Karl\Commands\ModelReplaces;

use Illuminate\Console\Command;
use kallbuloso\Karl\Helpers\Helpers;
use kallbuloso\Karl\Helpers\Progressbar;
use kallbuloso\Karl\Builder\ModelReplace\ModelReplaceTrait;

class ModelsDefaulCommnand extends Command
{
    use ModelReplaceTrait, Progressbar, Helpers;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'karl:make-model-default {model}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Return default Models from path "Model" or other of your choice';

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

        // verificando se a nova pasta existe
        $this->info("Checking Path \"{$modelName}\" if already exists.");
        if ($this->chekModelName($modelName) == false) {
            $this->output->newLine(2);
            $this->error("Path \"{$modelName}\" is not exist, canceling...");
            $this->output->newLine(1);
            return;
        }

        $this->startProgressBar(9, "Deleting path \"$modelName\" and returning defautl model User.php");

        // Replacing User namespace
        $this->makeProgress("Replacing User namespace");
        $this->replace(app_path($modelName.'\\User.php'), "App\\{$modelName}", 'App');

        // Trocando os diretórios
        $this->makeProgress("Return path $modelName end replacing model User.php");
        $this->exportAuthToNewPath($modelName);

        $paths = [
            'Auth/Register' => app_path('Http\\Controllers\\Auth\\RegisterController.php'),
            'Auth/Config' => base_path('config\\auth.php'),
            'Auth/Services' => base_path('config\\services.php'),
            'Auth/UserFactory' => base_path('database\\factories\\UserFactory.php'),
        ];

        foreach ($paths as $key => $path)
        {
            $this->makeProgress("Replacing file of $key");
            $this->replace($path, "App\\'.$modelName.'\\User", 'App\\User');
        }
        $this->makeProgress("Replacing path ModelCommand");
        $this->remDirModelCommand();

        $this->makeProgress("Replacing command in Kernel");
        $this->replace(
            app_path('Console\\kernel.php'),
            'Commands\ModelMakeCommand::class',
            ""
        );

        $this->finishProgress('Models default paths created successfully!');
        $this->dumpAutoloads();

    }

}
