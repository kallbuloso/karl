<?php
namespace kallbuloso\Karl\Commands\ResetDB;

class MakeResetDBCommand extends BaseLocalCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'karl:make-reset-db';
    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Reset all database data';
    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->call('migrate:fresh');
        $this->call('db:seed');
    }
}
