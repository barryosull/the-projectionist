<?php namespace ProjectonistTests\Unit\ValueObjects;

use Projectionist\ValueObjects\ProjectorMode;
use Projectionist\ValueObjects\ProjectorReference;
use ProjectonistTests\Fakes\Projectors\NoModeProjector;
use ProjectonistTests\Fakes\Projectors\RunOnce;

class ProjectorReferenceTest extends \PHPUnit_Framework_TestCase
{
    public function test_reads_mode_from_projector()
    {
        $ref = ProjectorReference::makeFromProjector(new RunOnce);

        $this->assertEquals(ProjectorMode::RUN_ONCE, $ref->mode);
    }

    public function test_gives_default_mode_if_none_set()
    {
        $ref = ProjectorReference::makeFromProjector(new NoModeProjector);

        $this->assertEquals(ProjectorMode::RUN_FROM_START, $ref->mode);
    }
}