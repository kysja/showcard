<?php

namespace Aulog\ScreenOrientation\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static void lockLandscape()
 * @method static void lockPortrait()
 *
 * @see \Aulog\ScreenOrientation\ScreenOrientation
 */
class ScreenOrientation extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \Aulog\ScreenOrientation\ScreenOrientation::class;
    }
}
