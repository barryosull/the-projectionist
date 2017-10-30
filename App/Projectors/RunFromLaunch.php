<?php namespace App\Projectors;

use App\ValueObjects\ProjectorMode;

class RunFromLaunch
{
    const MODE = ProjectorMode::RUN_FROM_LAUNCH;

    public static function version()
    {
        return 1;
    }
}