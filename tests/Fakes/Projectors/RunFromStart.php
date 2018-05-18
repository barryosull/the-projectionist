<?php namespace ProjectonistTests\Fakes\Projectors;

use Projectionist\Domain\ValueObjects\ProjectorMode;

class RunFromStart extends BaseTestProjector
{
    const MODE = ProjectorMode::RUN_FROM_START;

    protected static $projected_events = [];
}