<?php namespace ProjectonistTests\Unit\Strategy\EventHandler;

use Projectionist\Infra\EventHandler\ClassName;
use ProjectonistTests\Fakes\Projectors\BaseTestProjector;
use ProjectonistTests\Fakes\Services\EventLog\ThingHappened;

class ClassNameTest extends \PHPUnit\Framework\TestCase
{
    public function test_parses_handler_method_and_calls_it()
    {
        $projector = $this->prophesize(BaseTestProjector::class);
        $event = new ThingHappened('');

        $projector->whenThingHappened($event)->shouldBeCalled();

        $event_handler = new \Projectionist\Infra\EventHandler\ClassName();
        $event_handler->handle($event, $projector->reveal());
    }
}