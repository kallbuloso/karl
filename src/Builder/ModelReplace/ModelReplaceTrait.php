<?php

namespace kallbuloso\Karl\Builder\ModelReplace;

trait ModelReplaceTrait
{

    /**
     * Chek if exist Model Name
     *
     * @return void
     */
    public function chekModelName($modelName)
    {
        if ($this->laravel['files']->isDirectory($dir = app_path($modelName))) {
            return true;
        }
        return false;
    }

    /**
     * Export the auth to new path.
     *
     * @return void
     */
    public function exportAuthToNewPath($oldPath)
    {
        $getPath = app_path($oldPath.'/User.php');
        $setPath = app_path('User.php');
        copy($getPath, $setPath);

        @chmod($getPath, 0777);
        @unlink($getPath);
        @chmod($oldPath, 0777);
        @rmdir(app_path($oldPath));
    }

    /**
     * Export the auth to new path.
     *
     * @return void
     */
    protected function exportAuthToPath($path)
    {
        $this->makeDir($path);

        $getPath = app_path('User.php');
        $setPath = app_path($path .'/User.php');

        copy($getPath, $setPath);

        unlink($getPath);
    }

    /**
     * Create a directory if it doesn't exist.
     *
     * @param  string $path Path of the directory to make
     * @return bool
     */
    public function makeDir($path)
    {
        if (!$this->laravel['files']->isDirectory($dir = app_path($path))) {
            $this->laravel['files']->makeDirectory($dir, 0777, true);
        }
        return false;
    }

    /**
     * Set Model Command
     *
     * @return void
     */
    public function setModelCommand($dirCommand)
    {
        $getPath = __DIR__ .'/stubs/ModelMakeCommand.stub';
        $setPath = app_path($dirCommand);

        if (!file_exists(app_path('Console\\Commands\\ModelMakeCommand.php'))) {
            $this->makeDir('Console\\Commands');

            copy($getPath, $setPath);

            $this->replace($setPath, '{$rootNamespace}\\Models', '{$rootNamespace}\\'. $this->getArgName());
        }
    }

    /**
     * Set Model Command
     *
     * @return void
     */
    public function remDirModelCommand()
    {
        $getPath = app_path('Console/Commands/ModelMakeCommand.php');
        $commandPath = app_path("Console/Commands");

        chmod($getPath, 0777);
        unlink($getPath);
        chmod($commandPath, 0777);
        rmdir($commandPath);
    }

    /**
     * Open haystack, find and replace needles, save haystack.
     *
     * @param  string|array  $placeholder  String or array to look for (the needles)
     * @param  string|array $replacement What to replace the needles for?
     * @param  string|array $replacement What to replace the needles for?
     * @return $this
     */
    public function replace($getContent, $getReplace, $setReplacement)
    {
        $path = $getContent;

        if (file_exists($path)) {
            $file = file_get_contents($path);
            $file = str_replace($getReplace, $setReplacement, $file);

            file_put_contents($path, $file);
        }

        return $this;
    }

}
