<?php namespace Projectionist\Config\EventSourced;

use Projectionist\Adapter\Event;
use Projectionist\Adapter\EventStream;

class EventStore implements \Projectionist\Adapter\EventStore
{
    public function latestEvent(): Event
    {
        // TODO: Implement latestEvent() method.
    }

    public function getStream($last_event_id): EventStream
    {
        // TODO: Implement getStream() method.
    }
}