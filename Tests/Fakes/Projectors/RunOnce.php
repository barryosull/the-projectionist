<?php namespace Tests\Fakes\Projectors;

use Projectionist\ValueObjects\ProjectorMode;

class RunOnce extends BaseTestProjector
{
    const MODE = ProjectorMode::RUN_ONCE;
    const VERSION = 2;

    protected static $has_seen_event = false;
}