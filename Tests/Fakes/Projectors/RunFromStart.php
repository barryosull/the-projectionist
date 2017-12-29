<?php namespace Tests\Fakes\Projectors;

use Projectionist\ValueObjects\ProjectorMode;

class RunFromStart extends BaseTestProjector
{
    const MODE = ProjectorMode::RUN_FROM_START;

    protected static $has_seen_event = false;
}