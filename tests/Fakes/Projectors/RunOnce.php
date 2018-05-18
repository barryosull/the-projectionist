<?php namespace ProjectonistTests\Fakes\Projectors;

use Projectionist\Domain\ValueObjects\ProjectorMode;

class RunOnce extends BaseTestProjector
{
    const MODE = ProjectorMode::RUN_ONCE;
    const VERSION = 2;

    protected static $projected_events = [];
}