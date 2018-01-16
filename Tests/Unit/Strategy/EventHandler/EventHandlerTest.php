<?php namespace ProjectonistTests\Unit\Strategy\EventHandler;

use Projectionist\Strategy\EventHandler\ClassName;
use ProjectonistTests\Fakes\Services\EventStore\ThingHappened;

class EventHandlerTest extends \PHPUnit_Framework_TestCase
{
    public function test_handles_event_correctly()
    {
        $event_handler = new ClassName();

        $projector = $this->prophesize();

        $event = new ThingHappened('');

        $event_handler->handle($event, $projector->reveal();

        $projector->whenThingHappend($event)->shouldHaveBeenCalled();

    }
}