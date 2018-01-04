<?php namespace Projectionist\AdapterFactory\EventSourced;

use Projectionist\Services\EventStore\Event;
use Projectionist\Services\EventStore\EventStream;

class EventStore implements \Projectionist\Services\EventStore
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