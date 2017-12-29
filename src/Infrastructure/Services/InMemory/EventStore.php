<?php namespace Projectionist\Infrastructure\Services\InMemory;

class EventStore implements \Projectionist\Services\EventStore
{
    private static $events = [];

    public static function setEvents(array $events)
    {
        self::$events = $events;
    }

    public function latestEvent(): \Projectionist\Services\EventStore\Event
    {
        return last(self::$events);
    }

    public function getStream($last_event_id): \Projectionist\Services\EventStore\EventStream
    {
        return new EventStream(self::$events);
    }
}