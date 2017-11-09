<?php namespace Infrastructure\App\Services;

use App\Services\EventStore;
use App\ValueObjects\Event;
use Illuminate\Support\Collection;

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

    // TODO: Make Event collection
    public function getStream($last_event_id): Collection
    {
        return new Collection(self::$events);
    }
}