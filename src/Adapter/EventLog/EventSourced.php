<?php namespace Projectionist\Adapter\EventLog;

use Projectionist\Adapter\EventWrapper;
use Projectionist\Adapter\EventStream;

class EventSourced implements \Projectionist\Adapter\EventLog
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