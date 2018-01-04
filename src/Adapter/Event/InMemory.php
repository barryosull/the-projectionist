<?php namespace Projectionist\Adapter\Event;

use ProjectonistTests\Fakes\Services\EventStore\ThingHappened;

class InMemory implements \Projectionist\Adapter\Event
{
    private $wrapped_event;

    public function __construct(ThingHappened $event)
    {
        $this->wrapped_event = $event;
    }

    public function id()
    {
        return $this->wrapped_event->id();
    }

    public function content()
    {
        return $this->wrapped_event;
    }
}