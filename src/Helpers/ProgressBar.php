<?php
namespace kallbuloso\Karl\Helpers;

// use Symfony\Component\Console\Helper\ProgressBar;
// use Symfony\Component\Console\Style\OutputStyle;
// use Symfony\Component\Console\Style\SymfonyStyle;
trait ProgressBar
{
    protected $usleep = '100000';
    /**
     * Symfony ProgressBar instance.
     * @var object \Symfony\Component\Console\Helper\ProgressBar
     */
    protected $progressBar = null;

    /**
     * Setting custom formatting for the progress bar.
     *
     * @param  int $steps   The number of steps the progress bar has.
     * @return void
     */
    public function startProgressBar($steps, $message)
    {
        $this->progressBar = $this->output->createProgressBar($steps);
        // the finished part of the bar
        $this->progressBar->setBarCharacter('<comment>=</comment>');

        // the unfinished part of the bar
        $this->progressBar->setEmptyBarCharacter("-");

        // the progress character
        $this->progressBar->setProgressCharacter(">");

        // the 'layout' of the progress
        $this->progressBar->setFormat(" %current%/%max% [%bar%] %percent:3s% % ");

        // the 'Width' of the progress
        $this->progressBar->setBarWidth(50);

        $this->progressBar->start();
        $this->info($message);
        usleep($this->usleep);
        // sleep('1');
    }

    /**
     * Advance the progress bar with a step.
     *
     * @return void
     */
    public function makeProgress($message)
    {
        $this->progressBar->advance();
        $this->info($message);
        usleep($this->usleep);
        // sleep('1');
    }

    /**
     * Finalise the progress, output the (last) message.
     *
     * @param  string $message
     * @return void
     */
    public function finishProgress($message)
    {
        $this->progressBar->finish();
        $this->info($message);
        $this->output->newLine(1);
        $this->progressBar->clear();
    }
}
