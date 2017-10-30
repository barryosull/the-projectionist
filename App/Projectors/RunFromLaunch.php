<?php namespace App\Projectors;

use App\ValueObjects\ProjectorMode;

class RunFromLaunch extends BaseProjector
{
    const MODE = ProjectorMode::RUN_FROM_LAUNCH;

    protected static $has_seen_event = false;
}