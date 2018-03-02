<?php namespace ProjectonistTests\Unit\Fakes;

use ProjectonistTests\Fakes\Services\EventLog\ThingHappened;
use ProjectonistTests\Fakes\Projectors\RunFromLaunch;
use ProjectonistTests\Fakes\Projectors\RunFromStart;

class ProjectorTest extends \PHPUnit\Framework\TestCase
{
    private $event;

    const EVENT_ID = '94ae0b60-ddb4-4cf0-bb75-4b588fea3c3c';

    public function setUp()
    {
        $this->event = new ThingHappened(self::EVENT_ID);
    }

    public function test_can_check_if_fake_projector_got_event()
    {
        $projector_a = new RunFromStart();
        $projector_b = new RunFromLaunch();
        $projector_a->reset();
        $projector_b->reset();

        $projector_a->whenThingHappened($this->event);

        $this->assertTrue($projector_a->hasProjectedEvent(self::EVENT_ID));
        $this->assertFalse($projector_b->hasProjectedEvent(self::EVENT_ID));
    }

    public function test_can_reset_fake_projectors()
    {
        $projector_a = new RunFromStart();

        $projector_a->whenThingHappened($this->event);

        $projector_a->reset();

        $this->assertFalse($projector_a->hasProjectedEvent(self::EVENT_ID));
    }
}