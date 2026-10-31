<?php

namespace Aulog\ScreenOrientation;

use Illuminate\Support\ServiceProvider;

class ScreenOrientationServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(ScreenOrientation::class);
    }
}
