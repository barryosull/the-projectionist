<?php namespace ProjectonistTests\Projectionist\ValueObjects;

use Projectionist\Adapter\EventWrapper\Identifiable;
use Projectionist\ValueObjects\ProjectorPosition;
use Projectionist\ValueObjects\ProjectorReference;
use Projectionist\ValueObjects\ProjectorStatus;
use ProjectonistTests\Fakes\Projectors\RunOnce;
use ProjectonistTests\Fakes\Services\EventLog\ThingHappened;

class ProjectorPositionTest extends \PHPUnit\Framework\TestCase
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
        $ref = ProjectorReference::makeFromProjectorWithVersion(new RunOnce, 1);
        $processed_events = 2;
        $occurred_at = date('Y-m-d H:i:s');
        $last_event_id = '6c040404-80fd-4a4d-98d6-547344d4873a';
        $position = new ProjectorPosition($ref, $processed_events, $occurred_at, $last_event_id, ProjectorStatus::broken());

        $broken = $position->broken();

        $this->assertIsFailing($broken);
        $this->assertLastValidEventIdIsStillSet($last_event_id, $broken);
    }

    private function assertIsFailing(ProjectorPosition $actual)
    {
        $this->assertTrue($actual->isFailing());
    }

    private function assertLastValidEventIdIsStillSet($expected, ProjectorPosition $actual)
    {
        $this->assertEquals($expected, $actual->last_position);
    }

    public function test_marking_an_event_as_processed()
    {
        $ref = ProjectorReference::makeFromProjectorWithVersion(new RunOnce, 1);
        $event_id = '6c040404-80fd-4a4d-98d6-547344d4873a';
        $position = ProjectorPosition::makeNewUnplayed($ref);

        $position = $position->played(new Identifiable(new ThingHappened($event_id)));

        $this->assertEquals($event_id, $position->last_position);
        $this->assertEquals(1, $position->processed_events);
    }
}