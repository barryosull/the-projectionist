<?php namespace Projectionist\Infra\EventLog;

use Projectionist\Domain\Services\EventWrapper;
use Projectionist\Domain\Services\EventStream;

class EventSourced implements \Projectionist\Domain\Services\EventLog
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