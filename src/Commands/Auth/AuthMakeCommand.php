<?php

namespace kallbuloso\Karl\Commands\Auth;

use Illuminate\Console\Command;
use kallbuloso\Karl\Commands\BaseLocalCommand;
use kallbuloso\Karl\Builder\Auth\AuthMakeTrait;

class AuthMakeCommand extends BaseLocalCommand
{
    use  AuthMakeTrait;

    protected $usleep = '100000';
    /**
     * The name and signature of the console command.
     * @var string
     */
    protected $signature = 'karl:make-auth
                                {--c|confirm : Only scaffold the authentication confirmation at email}
                                {--f|force :  Overwrite existing files by default}';

    /**
     * The console command description.
     * @var string
     */
    protected $description = 'Scaffold basic login and registration views and routes';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->createDirectories();

        $this->exportViews();

        $this->exportLayout();

        $this->exportHome();

        $this->exportController();

        $this->exportRoute();

        $this->info('Authentication scaffolding generated successfully.');

    }
}
