<?php namespace App\Projectors;

use App\ValueObjects\ProjectorMode;

class RunFromStart extends BaseProjector
{
    const MODE = ProjectorMode::RUN_FROM_START;

    protected static $has_seen_event = false;
}