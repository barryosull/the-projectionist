<?php namespace ProjectonistTests\Projectionist\ValueObjects;

use Projectionist\ValueObjects\ProjectorPosition;
use Projectionist\ValueObjects\ProjectorReference;
use ProjectonistTests\Fakes\Projectors\RunOnce;

class ProjectorPositionTest extends \PHPUnit_Framework_TestCase
{
    public function test_make_a_new_unplayed_position_from_a_reference()
    {
        $reference = ProjectorReference::makeFromProjector(new RunOnce);
        $actual = ProjectorPosition::makeNewUnplayed($reference);

        $this->assertTrue($reference->equals($actual->projector_reference));
        $this->assertEmpty(0, $actual->processed_events);
    }

    public function test_marking_a_position_as_broken()
    {
        $ref = ProjectorReference::make(new RunOnce, 1);
        $processed_events = 2;
        $occurred_at = date('Y-m-d H:i:s');
        $last_event_id = '6c040404-80fd-4a4d-98d6-547344d4873a';
        $position = new ProjectorPosition($ref, $processed_events, $occurred_at, $last_event_id, false);

        $broken = $position->broken();

        $this->assertTrue($broken->is_broken);
    }
}