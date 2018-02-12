<?php namespace ProjectonistTests\Unit\Strategy\EventHandler;

use Projectionist\Strategy\EventHandler\ClassName;
use ProjectonistTests\Fakes\Projectors\BaseTestProjector;
use ProjectonistTests\Fakes\Services\EventLog\ThingHappened;

class ClassNameTest extends \PHPUnit_Framework_TestCase
{
    public function test_parses_handler_method_and_calls_it()
    {
        $projector = $this->prophesize(BaseTestProjector::class);
        $event = new ThingHappened('');

        $projector->whenThingHappened($event)->shouldBeCalled();

        $event_handler = new ClassName();
        $event_handler->handle($event, $projector->reveal());
    }
}