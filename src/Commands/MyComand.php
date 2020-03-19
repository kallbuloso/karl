<?php

namespace kallbuloso\Karl\Commands;

use Illuminate\Console\Command;

class MyCommand extends BaseLocalCommand
{
    /**
     * The name and signature of the console command.
     * @var string
     */
    protected $signature = 'karl:';

    /**
     * The console command description.
     * @var string
     */
    protected $description = 'description';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {

    }
}
