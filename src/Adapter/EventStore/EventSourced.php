<?php namespace Projectionist\Adapter\EventStore;

use Projectionist\Adapter\Event;
use Projectionist\Adapter\EventStream;

class EventSourced implements \Projectionist\Adapter\EventStore
{
    public function latestEvent(): Event
    {
        // TODO: Implement latestEvent() method.
    }

    public function getStream($last_event_id): EventStream
    {
        // TODO: Implement getStream() method.
    }

    public function hasEvents(): bool
    {
        // TODO: Implement hasEvents() method.
    }
}