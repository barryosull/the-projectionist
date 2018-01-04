<?php namespace Projectionist\AdapterFactory\InMemory;

use Projectionist\Services;

class EventStore implements Services\EventStore
{
    private static $events = [];

    public static function setEvents(array $events)
    {
        self::$events = $events;
    }

    public function hasEvents(): bool
    {
        return count(self::$events) != 0;
    }

    public function latestEvent(): Services\EventStore\Event
    {
        $event = last(self::$events);
        if (!$event) {
            throw new \Exception("No events in the EventStore");
        }
        return new Event($event);
    }

    public function getStream($last_event_id): Services\EventStore\EventStream
    {
        return new EventStream(self::$events);
    }


}