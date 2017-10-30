<?php namespace App\Projectors;

use App\ValueObjects\ProjectorMode;

class RunOnce extends BaseProjector
{
    const MODE = ProjectorMode::RUN_ONCE;
    const VERSION = 2;

    protected static $has_seen_event = false;
}