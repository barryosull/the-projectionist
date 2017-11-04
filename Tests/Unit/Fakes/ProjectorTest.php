<?php namespace Tests\Unit\Fakes;

use App\ValueObjects\Event;
use Tests\Fakes\Projectors\RunFromLaunch;
use Tests\Fakes\Projectors\RunFromStart;

class ProjectorTest extends \PHPUnit_Framework_TestCase
{
    private $event;

    public function setUp()
    {
        $this->event = new Event(
            '94ae0b60-ddb4-4cf0-bb75-4b588fea3c3c',
            'domain.context.aggregate.event',
            '2017-01-01 00:00:01',
            new \stdClass()
        );
    }

    public function test_can_check_if_fake_projector_got_event()
    {
        $projector_a = new RunFromStart();
        $projector_b = new RunFromLaunch();
        $projector_a->reset();
        $projector_b->reset();

        $projector_a->when_domain_context_aggregate_event($this->event->body, $this->event);

        $this->assertTrue($projector_a->hasSeenEvent());
        $this->assertFalse($projector_b->hasSeenEvent());
    }

    public function test_can_reset_fake_projectors()
    {
        $projector_a = new RunFromStart();

        $projector_a->when_domain_context_aggregate_event($this->event->body, $this->event);

        $projector_a->reset();

        $this->assertFalse($projector_a->hasSeenEvent());
    }
}