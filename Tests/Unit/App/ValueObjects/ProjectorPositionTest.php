<?php namespace Tests\Unit\ValueObjects;

use App\ValueObjects\ProjectorPosition;
use App\ValueObjects\ProjectorReference;
use Tests\Fakes\Projectors\RunOnce;

class ProjectorPositionTest extends \PHPUnit_Framework_TestCase
{
    public function test_make_a_new_unplayed_position_from_a_reference()
    {
        $reference = ProjectorReference::makeFromClass(RunOnce::class);
        $actual = ProjectorPosition::makeNewUnplayed($reference);

        $this->assertEquals($reference, $actual->projector_reference);
        $this->assertEquals($reference->version, $actual->projector_version);
        $this->assertEmpty(0, $actual->processed_events);
    }

    public function test_bump_version()
    {
        $position = ProjectorPosition::makeNewUnplayed(ProjectorReference::makeFromClass(RunOnce::class));

        $bumped = $position->bumpVersion();

        $this->assertEquals($position->projector_version+1, $bumped->projector_version);
    }
}