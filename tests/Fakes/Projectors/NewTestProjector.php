<?php namespace ProjectonistTests\Fakes\Projectors;

use Projectionist\Domain\ValueObjects\ProjectorMode;

class NewTestProjector extends BaseTestProjector
{
    const MODE = ProjectorMode::RUN_FROM_START;
    const VERSION = 1;
}