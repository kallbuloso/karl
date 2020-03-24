<?php

namespace kallbuloso\Karl\Commands\Auth;

use Illuminate\Console\Command;
use kallbuloso\Karl\Commands\BaseLocalCommand;
use kallbuloso\Karl\Builder\Auth\ConfirmMakeTrait;

class ConfirmMakeCommand extends BaseLocalCommand
{
    use  ConfirmMakeTrait;

    protected $usleep = '1000000';
    /**
     * The name and signature of the console command.
     * @var string
     */
    protected $signature = 'karl:make-auth-confirm
                                {--f|force :  Overwrite existing files by default}';

    /**
     * The console command description.
     * @var string
     */
    protected $description = 'Scaffold full email confirmations';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->startProgressBar(13, "Install Mail confirm => Initiating...");

        // Directories
        $this->makeProgress("Creating Directoties...");
        $this->createDirectories();
        $this->makeProgress("Directories created as successfully");

        // Controllers
        $this->makeProgress("Creating Controllers...");
        $this->exportApps();
        $this->makeProgress("Controllers created as successfully");

        // Mails
        $this->makeProgress('Creating Mails...');
        $this->exportMail();
        $this->makeProgress("Mails created as successfully");

        // Models
        $this->makeProgress('Creating Model...');
        $this->exportModels();
        $this->makeProgress("Model created as successfully");

        // Migrations
        $this->makeProgress('Creating Migrations...');
        $this->exportMigrations();
        $this->makeProgress("Migrations created as successfully");

        // Routes
        $this->makeProgress("Creating Routes...");
        $this->exportRoute();
        $this->makeProgress("Routes created as successfully");

        $this->finishProgress('Mail Confirm scaffolding generated successfully.');
        // $this->info('Authentication scaffolding generated successfully.');

    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    private function makeSimpleAuth()
    {

    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    private function makeConfirmAuth()
    {

    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    private function ambiguosAuth()
    {

    }
}
