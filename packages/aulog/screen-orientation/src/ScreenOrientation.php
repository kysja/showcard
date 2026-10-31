<?php

namespace Aulog\ScreenOrientation;

class ScreenOrientation
{
    public function lockLandscape(): void
    {
        $this->call('ScreenOrientation.LockLandscape');
    }

    public function lockPortrait(): void
    {
        $this->call('ScreenOrientation.LockPortrait');
    }

    private function call(string $function): void
    {
        if (function_exists('nativephp_call')) {
            nativephp_call($function, '{}');
        }
    }
}
