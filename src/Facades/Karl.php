<?php

namespace kallbuloso\Karl\Facades;

use Illuminate\Support\Facades\Facade;

class Karl extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'karl';
    }
}
