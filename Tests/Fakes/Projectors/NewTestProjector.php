<?php namespace Tests\Fakes\Projectors;

use Projectionist\App\ValueObjects\ProjectorMode;

class NewTestProjector extends BaseTestProjector
{
    const MODE = ProjectorMode::RUN_FROM_START;
    const VERSION = 1;
}