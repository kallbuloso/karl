<?php

namespace kallbuloso\Karl\Commands\Schema;

use kallbuloso\Karl\Helpers\Helpers;
use kallbuloso\Karl\Builder\Schema\MakeSchemaTrait;
use kallbuloso\Karl\Commands\BaseLocalCommand;

class MakeSchemaCommand extends BaseLocalCommand
{
    use MakeSchemaTrait;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'karl:make-schema';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Set Schema in AppServiceProvider';

    /**
     * Execute the console command.
     *
     * @return void
     *
     * @throws \InvalidArgumentException
     */
    public function handle()
    {
        $this->startProgressBar(3, "Replacing Schema in AppServiceProvider");
        $this->makeProgress('Loading...');

        $this->makeSchema();
        $this->makeProgress('Finish...');

        $this->finishProgress('Replacing Schema as successfully!');
        $this->dumpAutoloads();
    }
}
