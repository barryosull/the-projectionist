<?php namespace Projectionist\Adapter\EventLog;

use Projectionist\Adapter\EventWrapper;
use Projectionist\Adapter\EventStream;

class InMemory implements \Projectionist\Adapter\EventLog
{
    private static $events = [];

    public function appendEvent($event)
    {
        self::$events[] = $event;
    }

    public function reset()
    {
        self::$events = [];
    }

    public function hasEvents(): bool
    {
        return count(self::$events) != 0;
    }

    public function latestEvent(): \Projectionist\Adapter\EventWrapper
    {
        $event = last(self::$events);
        if (!$event) {
            throw new \Exception("No events in the EventLog");
        }
        return new EventWrapper\Identifiable($event);
    }

    public function getStream($last_event_id): \Projectionist\Adapter\EventStream
    {
        return new EventStream\InMemory(self::$events);
    }


}