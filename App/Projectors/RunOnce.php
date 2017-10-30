<?php namespace App\Projectors;

use App\ValueObjects\ProjectorMode;

class RunOnce
{
    const MODE = ProjectorMode::RUN_ONCE;

    public static function version()
    {
        return 2;
    }
}