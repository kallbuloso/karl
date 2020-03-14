<?php

namespace kallbuloso\Karl\Commands;

use Illuminate\Console\Command;
use kallbuloso\Karl\Helpers\Helpers;
use kallbuloso\Karl\Helpers\Progressbar;
use kallbuloso\Karl\Builder\ModelReplace\ModelReplaceTrait;

class ModelsRewriteCommand extends Command
{
    use ModelReplaceTrait, Progressbar, Helpers;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'karl:make-model-rewrite  {model} {newName}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Rewrite default Models path if exist';

    /**
     * Execute the console command.
     *
     * @return void
     *
     * @throws \InvalidArgumentException
     */
    public function handle()
    {
        $oldModelName = $this->argument('model');
        $modelName = $this->argument('newName');

        // verifica se os Model têm o mesmo nome
        $this->info("Cheking Models Path Name if exists");
        if ($oldModelName == $modelName) {
            $this->output->newLine(1);
            $this->error("As duas pastas têm o mesmo nome. Tá de zoeira?");
            $this->output->newLine(1);
            return;
        }

        // verificando se a pasta antiga existe
        $this->info("Cheking Old Path {$oldModelName} if already exists");
        if ($this->chekOldModelName($oldModelName) == false) {
            $this->output->newLine(2);
            $this->error("Old Path \"{$oldModelName}\" is not exists, canceling...");
            $this->output->newLine(1);
            return;
        }

        // verificando se a nova pasta existe
        $this->info("New Path \"{$modelName}\" if already exists.");
        if ($this->chekNewModelName($modelName) == true) {
            $this->output->newLine(2);
            $this->error("New Path \"{$modelName}\" already exists, canceling...");
            $this->output->newLine(1);
            return;
        }

        $this->startProgressBar(8, "Rewrite model \"$modelName\" path to $oldModelName");

        // Trocando os diretórios
        $this->makeProgress("Creating path $modelName end replacing model User.php");
        $this->exportReplaceAuthToNewPath($oldModelName, $modelName);

        // Replacing User namespace
        $this->makeProgress("Replacing User namespace");
        $this->replace(app_path($modelName.'\\User.php'), "App\\{$oldModelName}", 'App\\'. $modelName);

        $paths = [
            'Auth/Register' => app_path('Http\\Controllers\\Auth\\RegisterController.php'),
            'Auth/Config' => base_path('config\\auth.php'),
            'Auth/Services' => base_path('config\\services.php'),
            'Auth/UserFactory' => base_path('database\\factories\\UserFactory.php'),
        ];

        foreach ($paths as $key => $path)
        {
            $this->makeProgress("Replacing file of $key");
            $this->replace($path, "App\\{$oldModelName}\\User", 'App\\'.$modelName.'\\User');
        }

        $this->makeProgress("Replacing path ModelCommand");
        $this->setReplaceModelCommand($oldModelName, $modelName);

        // Finished creating the package, end of the progress bar
        $this->finishProgress('Replace Model Path is successfully!');
        $this->dumpAutoloads();


    }

}
