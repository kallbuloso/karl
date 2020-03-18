<?php
namespace kallbuloso\Karl\Commands;

use Exception;
use Illuminate\Console\Command;
use kallbuloso\Karl\Helpers\ProgressBar;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

abstract class BaseLocalCommand extends Command
{
    use ProgressBar;

    protected $usleep = '100000';

    protected $name = '';
    /**
     * The name and signature of the console command.
     * @var string
     */
    protected $signature = '';

    /**
     * The console command description.
     * @var string
     */
    protected $description = '';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {

    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        if (!app()->isLocal()) {
            if (!$this->confirm(__('Are you sure run this command in a non-local environment?'))) {
                die(2);
            }
        }
        return parent::execute($input, $output);
    }
}
