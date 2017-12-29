<?php namespace Projectionist\Infrastructure\App\Services\InMemory;

class EventStore implements \Projectionist\App\Services\EventStore
{
    private static $events = [];

    public static function setEvents(array $events)
    {
        self::$events = $events;
    }

    public function latestEvent(): \Projectionist\App\Services\EventStore\Event
    {
        return last(self::$events);
    }

    public function getStream($last_event_id): \Projectionist\App\Services\EventStore\EventStream
    {
        return new EventStream(self::$events);
    }
}