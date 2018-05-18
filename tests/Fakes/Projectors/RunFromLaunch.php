<?php namespace ProjectonistTests\Fakes\Projectors;

use Projectionist\Domain\ValueObjects\ProjectorMode;

class RunFromLaunch extends BaseTestProjector
{
    const MODE = ProjectorMode::RUN_FROM_LAUNCH;

    protected static $projected_events = [];
}