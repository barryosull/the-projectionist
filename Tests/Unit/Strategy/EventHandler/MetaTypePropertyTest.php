<?php namespace ProjectonistTests\Unit\Strategy\EventHandler;

use Projectionist\Strategy\EventHandler\ClassName;
use Projectionist\Strategy\EventHandler\MetaTypeProperty;
use ProjectonistTests\Fakes\Projectors\BaseTestProjector;
use ProjectonistTests\Fakes\Services\EventLog\ThingHappened;

class MetaTypePropertyTest extends \PHPUnit_Framework_TestCase
{
    public function test_parses_handler_method_and_calls_it()
    {
        $projector = $this->prophesize(MetaTypeProjector::class);

        $snapshot = new class {
            public $event = 'event-data';

            public function type(){
                return 'type.of.event';
            }
        };

        $projector->when_type_of_event('event-data', $snapshot)->shouldBeCalled();

        $event_handler = new MetaTypeProperty();
        $event_handler->handle($snapshot, $projector->reveal());
    }
}

interface MetaTypeProjector {
    public function when_type_of_event($event, $snapshot);
}