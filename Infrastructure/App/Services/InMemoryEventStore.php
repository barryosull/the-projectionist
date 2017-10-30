<?php namespace Infrastructure\App\Services;

use App\Services\EventStore;
use Illuminate\Support\Collection;

class InMemoryEventStore implements EventStore
{
    private static $events = [];

    public static function setEvents(array $events)
    {
        self::$events = $events;
    }

    public function latestEvent()
    {
        return last(self::$events);
    }

    public function getStream($last_event_id): Collection
    {
        return new Collection(self::$events);
    }
}