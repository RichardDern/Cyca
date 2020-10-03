<?php

namespace App\Facades;

use Illuminate\Support\Facades\Facade;
use App\Services\ThemeManager as BaseClass;

class ThemeManager extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {return BaseClass::class;}
}
