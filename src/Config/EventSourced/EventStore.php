<?php namespace Projectionist\Config\EventSourced;

use Projectionist\Adapter\EventStore\Event;
use Projectionist\Adapter\EventStore\EventStream;

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