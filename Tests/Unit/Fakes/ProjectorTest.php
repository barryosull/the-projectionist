<?php namespace Tests\Unit\Fakes;

use App\ValueObjects\Event;
use Tests\Fakes\Projectors\RunFromLaunch;
use Tests\Fakes\Projectors\RunFromStart;

class ProjectorTest extends \PHPUnit_Framework_TestCase
{
    public function test_can_check_if_projector_got_event()
    {
        $event = new Event(
            '94ae0b60-ddb4-4cf0-bb75-4b588fea3c3c',
            'domain.context.aggregate.event',
            '2017-01-01 00:00:01',
            new \stdClass()
        );

        $projector_a = new RunFromStart();
        $projector_b = new RunFromLaunch();

        $projector_a->when_domain_context_aggregate_event($event->body, $event);

        $this->assertTrue($projector_a->hasSeenEvent());
        $this->assertFalse($projector_b->hasSeenEvent());
    }
}