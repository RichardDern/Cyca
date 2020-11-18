<?php

namespace App\Facades;

use App\Services\ThemeManager as BaseClass;
use Illuminate\Support\Facades\Facade;

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
