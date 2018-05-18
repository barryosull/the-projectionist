<?php namespace Projectionist\Infra\EventLog;

use Projectionist\Infra\EventWrapper;
use Projectionist\Infra\EventStream;

class InMemory implements \Projectionist\Domain\Services\EventLog
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

    public function latestEvent(): \Projectionist\Domain\Services\EventWrapper
    {
        $event = last(self::$events);
        if (!$event) {
            return null;
        }
        return new EventWrapper\Identifiable($event);
    }

    public function getStream($last_event_id): \Projectionist\Domain\Services\EventStream
    {
        return new EventStream\InMemory(self::$events);
    }
}