<?php

namespace kallbuloso\Karl\Helpers;

use Illuminate\Filesystem\Filesystem;


trait Helpers
{
    /**
     * The filesystem instance.
     *
     * @var \Illuminate\Filesystem\Filesystem
     */
    protected $files;

    /**
     * Create a new key generator command.
     *
     * @param  \Illuminate\Filesystem\Filesystem  $files
     * @return void
     */
    public function __construct(Filesystem $files)
    {
        parent::__construct();

        $this->files = $files;
    }

    /**
     * Replace the given string in the given file.
     *
     * @param  string  $path
     * @param  string|array  $search
     * @param  string|array  $replace
     * @return void
     */
    protected function replaceIn($path, $search, $replace)
    {
        if ($this->files->exists($path)) {
            $this->files->put($path, str_replace($search, $replace, $this->files->get($path)), true);
        }
        /*
        * Example at use
        *
        protected function replaceNamespace($path)
        {
            $search = [
                'namespace '.$this->currentRoot.';',
                $this->currentRoot.'\\',
            ];

            $replace = [
                'namespace '.$this->argument('name').';',
                $this->argument('name').'\\',
            ];

            $this->replaceIn($path, $search, $replace);
        }*/

    }
    /**
     * Dump Composer's autoloads.
     */
    public function dumpAutoloads()
    {
        shell_exec('composer dump-autoload');
    }

    /**
     * @return bool
     */
    protected function runProcess(array $command)
    {
        $process = new \Symfony\Component\Process\Process($command, base_path());
        $process->run();

        return $process->getExitCode() === 0;
    }

}
