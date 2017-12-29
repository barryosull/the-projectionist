<?php namespace Tests\Unit\ValueObjects;

use Projectionist\App\ValueObjects\ProjectorPosition;
use Projectionist\App\ValueObjects\ProjectorReference;
use Tests\Fakes\Projectors\RunOnce;

class ProjectorPositionTest extends \PHPUnit_Framework_TestCase
{
    public function test_make_a_new_unplayed_position_from_a_reference()
    {
        $reference = ProjectorReference::makeFromClass(RunOnce::class);
        $actual = ProjectorPosition::makeNewUnplayed($reference);

        $this->assertTrue($reference->equals($actual->projector_reference));
        $this->assertEmpty(0, $actual->processed_events);
    }
}