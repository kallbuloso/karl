<?php

namespace kallbuloso\Karl\Helpers;


trait Helpers
{
    /**
     * Dump Composer's autoloads.
     */
    public function dumpAutoloads()
    {
        shell_exec('composer dump-autoload');
    }

}
