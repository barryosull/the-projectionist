<?php namespace Infrastructure\App\Services;

use App\Services\EventStore;
use App\Services\EventStore\Event;
use App\Services\EventStore\EventStream;

class InMemoryEventStore implements EventStore
{
    private static $events = [];

    public static function setEvents(array $events)
    {
        self::$events = $events;
    }

    public function latestEvent(): Event
    {
        return last(self::$events);
    }

    public function getStream($last_event_id): EventStream
    {
        return new InMemoryEventStream(self::$events);
    }
}