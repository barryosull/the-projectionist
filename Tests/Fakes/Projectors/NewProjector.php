<?php namespace Tests\Fakes\Projectors;

use App\ValueObjects\ProjectorMode;

class NewProjector extends BaseProjector
{
    const MODE = ProjectorMode::RUN_FROM_START;
    const VERSION = 1;
}