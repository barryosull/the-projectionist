<?php namespace ProjectonistTests\Fakes\Projectors;

use Projectionist\ValueObjects\ProjectorMode;

class RunFromLaunch extends BaseTestProjector
{
    const MODE = ProjectorMode::RUN_FROM_LAUNCH;

    protected static $has_seen_event = false;
}