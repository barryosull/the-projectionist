<?php namespace Projectionist\Infra\EventLog;

use Projectionist\Domain\Services\EventWrapper;
use Projectionist\Domain\Services\EventStream;

class EventSourced implements \Projectionist\Domain\Services\EventLog
{
    public function latestEvent(): Event
    {
        // TODO: Implement latestEvent() method.
    }

    public function getStream($lastEventId): EventStream
    {
        // TODO: Implement getStream() method.
    }
}