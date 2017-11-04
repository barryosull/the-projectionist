<?php namespace Tests\Fakes\Projectors;

use App\ValueObjects\ProjectorMode;

class RunFromLaunch extends BaseTestProjector
{
    const MODE = ProjectorMode::RUN_FROM_LAUNCH;

    protected static $has_seen_event = false;
}