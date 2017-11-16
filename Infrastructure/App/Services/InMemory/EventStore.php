<?php namespace Infrastructure\App\Services\InMemory;

class EventStore implements \App\Services\EventStore
{
    private static $events = [];

    public static function setEvents(array $events)
    {
        self::$events = $events;
    }

    public function latestEvent(): \App\Services\EventStore\Event
    {
        return last(self::$events);
    }

    public function getStream($last_event_id): \App\Services\EventStore\EventStream
    {
        return new EventStream(self::$events);
    }
}