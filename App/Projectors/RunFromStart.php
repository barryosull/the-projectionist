<?php namespace App\Projectors;

use App\ValueObjects\ProjectorMode;

class RunFromStart
{
    const MODE = ProjectorMode::RUN_FROM_START;

    public static function version()
    {
        return 1;
    }
}