<?php namespace Tests\App\ValueObjects;

use App\ValueObjects\ProjectorPosition;
use App\ValueObjects\ProjectorReference;
use Tests\Fakes\Projectors\RunOnce;

class ProjectorPositionTest extends \PHPUnit_Framework_TestCase
{
    public function test_marking_a_position_as_broken()
    {
        $ref = ProjectorReference::make(RunOnce::class, 1);
        $processed_events = 2;
        $occurred_at = date('Y-m-d H:i:s');
        $last_event_id = '6c040404-80fd-4a4d-98d6-547344d4873a';
        $position = new ProjectorPosition($ref, $processed_events, $occurred_at, $last_event_id, false);

        $broken = $position->broken();

        $this->assertTrue($broken->is_broken);
    }
}