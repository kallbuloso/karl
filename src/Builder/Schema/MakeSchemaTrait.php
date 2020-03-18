<?php

namespace kallbuloso\Karl\Builder\Schema;

use kallbuloso\Karl\Helpers\Helpers;
use kallbuloso\Karl\Helpers\ProgressBar;

trait MakeSchemaTrait
{
    use ProgressBar, Helpers;

    protected function makeSchema()
    {
        $path = app_path('Providers\\AppServiceProvider.php');
        $stubPath = __DIR__ .'/stubs/AppServiceProvider.stub';

        $contentPath = file_get_contents($path);

        $search = $contentPath;
        $replace = file_get_contents($stubPath);

        $this->replaceIn($path, $search, $replace);
    }
}
